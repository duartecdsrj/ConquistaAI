<?php
declare(strict_types=1);

use App\Application\Review\DTO\Request\CreateOrReuseFlashcardRequestDto;
use App\Application\Review\Service\CreateOrReuseFlashcardService;
use App\Domain\Review\Entity\UserFlashcardProgress;
use App\Domain\Review\Enum\FlashcardSource;
use App\Domain\Review\Enum\FlashcardType;
use App\Domain\Review\ValueObject\FlashcardFingerprint;
use App\Domain\Review\ValueObject\FlashcardProgressState;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineUserRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineFlashcardRepository;
use App\Infrastructure\Persistence\Doctrine\Review\DoctrineUserFlashcardProgressRepository;
use App\Infrastructure\Persistence\Doctrine\Review\Entity\ReviewSessionRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;

require __DIR__ . '/../vendor/autoload.php';

[$script, $email, $subjectSlug, $mode] = array_pad($argv, 4, null);
$subjectSlug ??= 'ingles';
$archiveOthers = $mode === '--archive-others';
if (!is_string($email) || trim($email) === '' || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $subjectSlug)) {
    fwrite(STDERR, "Uso: php bin/import-flashcards-json.php <email> [slug-do-assunto] [--archive-others] < deck.json\n");
    exit(64);
}

try {
    $payload = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    fwrite(STDERR, "O arquivo não contém um JSON válido.\n");
    exit(65);
}

$deck = is_array($payload['deck'] ?? null) ? $payload['deck'] : $payload;
$items = $deck['cards'] ?? null;
if (!is_array($items) || $items === [] || (isset($deck['total_cards']) && $deck['total_cards'] !== count($items))) {
    fwrite(STDERR, "O deck precisa conter cards válidos e total_cards compatível.\n");
    exit(65);
}

$cards = [];
foreach ($items as $index => $item) {
    if (!is_array($item) || !is_string($item['front'] ?? null) || !is_string($item['back'] ?? null)) {
        fwrite(STDERR, sprintf("Card inválido na posição %d.\n", $index + 1));
        exit(65);
    }
    $front = trim($item['front']);
    $back = trim($item['back']);
    if ($front === '' || $back === '') {
        fwrite(STDERR, sprintf("Card vazio na posição %d.\n", $index + 1));
        exit(65);
    }
    $cards[] = ['front' => $front, 'back' => $back];
}

$entityManager = DoctrineEntityManagerFactory::create();
$users = new DoctrineUserRepository($entityManager);
$subjects = new DoctrineTaxonomySubjectRepository($entityManager);
$flashcards = new DoctrineFlashcardRepository($entityManager);
$progresses = new DoctrineUserFlashcardProgressRepository($entityManager);
$user = $users->findByEmail(mb_strtolower(trim($email)));
if ($user === null || !$user->isActive()) {
    fwrite(STDERR, "Usuário ativo não encontrado.\n");
    exit(66);
}

$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$result = (new DoctrineTransactionManager($entityManager))->transactional(function () use ($cards, $flashcards, $progresses, $subjects, $subjectSlug, $user, $now, $archiveOthers, $entityManager): array {
    $subject = $subjects->findBySlug($subjectSlug);
    $subjectCreated = false;
    if ($subject === null) {
        $subject = new TaxonomySubject(uuid(), null, 'Inglês', $subjectSlug, 'Conteúdos de língua inglesa.', 0, true);
        $subjects->save($subject);
        $subjectCreated = true;
    }

    $service = new CreateOrReuseFlashcardService($flashcards, $subjects);
    $created = $reused = $progressCreated = $progressReused = $progressReactivated = 0;
    $retainedCardIds = [];
    foreach ($cards as $item) {
        $fingerprint = FlashcardFingerprint::fromContent($subject->id, FlashcardType::BASIC, $item['front'], $item['back']);
        $existing = $flashcards->findByFingerprint($subject->id, $fingerprint);
        $card = $service->execute(new CreateOrReuseFlashcardRequestDto($subject->id, $item['front'], $item['back'], FlashcardSource::EDITORIAL), $now);
        $retainedCardIds[$card->id] = true;
        $existing === null ? $created++ : $reused++;
        $progress = $progresses->find($user->id, $card->id);
        if ($progress === null) {
            $progresses->save(new UserFlashcardProgress($user->id, $card->id, new FlashcardProgressState($now, 0, 2.5, 0, 0), null, $now, $now));
            $progressCreated++;
        } else {
            if (!$progress->active) {
                $progress->activate($now);
                $progresses->save($progress);
                $progressReactivated++;
            }
            $progressReused++;
        }
    }
    $archivedProgress = $abandonedSessions = 0;
    if ($archiveOthers) {
        $archivedProgress = $progresses->archiveExcept($user->id, array_keys($retainedCardIds), $now);
        $sessions = $entityManager->getRepository(ReviewSessionRecord::class)->findBy(['userId' => $user->id, 'status' => 'ACTIVE']);
        foreach ($sessions as $session) {
            $session->status = 'ABANDONED';
            $session->completedAt = $now;
            $abandonedSessions++;
        }
    }
    return compact('subject', 'subjectCreated', 'created', 'reused', 'progressCreated', 'progressReused', 'progressReactivated', 'archivedProgress', 'abandonedSessions');
});

echo json_encode([
    'deck' => $deck['name'] ?? $deck['title'] ?? null,
    'cards' => count($cards),
    'subject' => ['id' => $result['subject']->id, 'name' => $result['subject']->name, 'created' => $result['subjectCreated']],
    'flashcards' => ['created' => $result['created'], 'reused' => $result['reused']],
    'progress' => ['created' => $result['progressCreated'], 'reused' => $result['progressReused'], 'reactivated' => $result['progressReactivated'], 'due_at' => $now->format(DATE_ATOM)],
    'archiving' => $archiveOthers ? ['archived_progress' => $result['archivedProgress'], 'abandoned_sessions' => $result['abandonedSessions']] : null,
], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function uuid(): string
{
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
}
