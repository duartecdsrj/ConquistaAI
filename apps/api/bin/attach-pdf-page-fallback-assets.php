<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionAssetRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

[$script, $jobId, $assetDirectory] = array_pad($argv, 3, null);
if (!is_string($jobId) || !is_string($assetDirectory) || $jobId === '' || $assetDirectory === '') throw new InvalidArgumentException('Informe job e diretório de ativos.');
$em = DoctrineEntityManagerFactory::create();
$questions = $em->createQueryBuilder()->select('question')->from(QuestionRecord::class, 'question')->where('question.sourcePdfJobId = :job')->setParameter('job', $jobId)->getQuery()->getResult();
$created = 0;
foreach ($questions as $question) {
    if (!$question instanceof QuestionRecord) continue;
    foreach ((array) $question->sourcePdfPages as $page) {
        $page = (int) $page;
        $path = $assetDirectory . '/page-' . $page . '-cairo.png';
        if ($page < 1 || !is_file($path)) continue;
        $exists = $em->createQueryBuilder()->select('COUNT(asset.id)')->from(QuestionAssetRecord::class, 'asset')->where('asset.questionId = :question')->andWhere('asset.optionId IS NULL')->andWhere('asset.pageNumber = :page')->setParameter('question', $question->id)->setParameter('page', $page)->getQuery()->getSingleScalarResult();
        if ((int) $exists > 0) continue;
        $asset = new QuestionAssetRecord();
        $asset->id = uuid();
        $asset->questionId = $question->id;
        $asset->pageNumber = $page;
        $asset->path = $path;
        $asset->mimeType = 'image/png';
        $asset->sortOrder = 1;
        $asset->createdAt = new DateTimeImmutable('now');
        $em->persist($asset);
        $created++;
    }
}
$em->flush();
echo json_encode(['created_assets' => $created], JSON_UNESCAPED_UNICODE) . PHP_EOL;

function uuid(): string { $bytes = random_bytes(16); $bytes[6] = chr((ord($bytes[6]) & 15) | 64); $bytes[8] = chr((ord($bytes[8]) & 63) | 128); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4)); }
