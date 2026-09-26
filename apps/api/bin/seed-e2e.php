<?php
declare(strict_types=1);

use App\Infrastructure\Database;

require __DIR__ . '/../vendor/autoload.php';

$pdo = Database::pdo();
$existing = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$ids = [
    'user' => '10000000-0000-0000-0000-000000000001',
    'exam' => '20000000-0000-0000-0000-000000000002',
    'syllabus' => '30000000-0000-0000-0000-000000000002',
    'subject' => '40000000-0000-0000-0000-000000000002',
    'notebook' => '50000000-0000-0000-0000-000000000002',
];
$password = (string) (getenv('E2E_PASSWORD') ?: 'VisualTest#2026');
$email = (string) (getenv('E2E_EMAIL') ?: 'visual@conquistaai.test');
$now = gmdate('Y-m-d H:i:s');
$existing->execute([$email]);
$existingUserId = $existing->fetchColumn();
if (is_string($existingUserId) && $existingUserId !== '') $ids['user'] = $existingUserId;
$fixture = $pdo->prepare('SELECT 1 FROM notebooks WHERE id = ? AND user_id = ?');
$fixture->execute([$ids['notebook'], $ids['user']]);
if ($fixture->fetchColumn()) { echo "E2E fixture already loaded\n"; exit(0); }

$pdo->beginTransaction();
try {
    if ($existingUserId === false) {
        $pdo->prepare('INSERT INTO users (id, email, name, password_hash, status, created_at, updated_at) VALUES (?, ?, ?, ?, "ACTIVE", ?, ?)')
            ->execute([$ids['user'], $email, 'Usuário Visual', password_hash($password, PASSWORD_ARGON2ID), $now, $now]);
    }
    $assignRole = $pdo->prepare('INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)');
    $assignRole->execute([$ids['user'], '00000000-0000-0000-0000-000000000001']);
    $assignRole->execute([$ids['user'], '00000000-0000-0000-0000-000000000002']);
    $pdo->prepare('INSERT INTO exams (id, name, organizer, year, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$ids['exam'], 'Receita Federal', 'Cebraspe', 2023, $now, $now]);
    $pdo->prepare('INSERT INTO syllabi (id, exam_id, name, published_at, source_url, created_at, updated_at) VALUES (?, ?, ?, ?, NULL, ?, ?)')
        ->execute([$ids['syllabus'], $ids['exam'], 'Receita Federal - 2023', '2023-01-01', $now, $now]);
    $pdo->prepare('INSERT INTO subjects (id, syllabus_id, parent_id, name, sort_order, created_at, updated_at) VALUES (?, ?, NULL, ?, 1, ?, ?)')
        ->execute([$ids['subject'], $ids['syllabus'], 'Direito Tributário', $now, $now]);

    $questions = [
        'De acordo com o princípio da legalidade tributária, é correto afirmar que a instituição ou majoração de tributos depende de lei em sentido estrito.',
        'Sobre espécies tributárias, assinale a alternativa que descreve corretamente a taxa.',
        'A competência tributária é definida pela Constituição Federal e não pode ser livremente transferida.',
        'O imposto possui fato gerador independente de uma atividade estatal específica relativa ao contribuinte.',
        'A imunidade tributária é limitação constitucional ao poder de tributar.',
        'O lançamento tributário verifica a ocorrência do fato gerador e calcula o montante devido.',
        'A obrigação tributária principal surge com a ocorrência do fato gerador previsto em lei.',
        'A responsabilidade tributária pode alcançar terceiro expressamente previsto em lei.',
        'A taxa não pode ter base de cálculo própria de imposto.',
        'O crédito tributário decorre da obrigação principal e tem a mesma natureza desta.',
    ];
    $questionInsert = $pdo->prepare('INSERT INTO questions (id, syllabus_id, statement, difficulty, board, exam_year, source, reference_url, origin, status, correct_option_id, created_by, created_at, updated_at) VALUES (?, ?, ?, "MEDIUM", "Cebraspe", 2023, "Fixture E2E", NULL, "ORIGINAL", "PUBLISHED", NULL, ?, ?, ?)');
    $optionInsert = $pdo->prepare('INSERT INTO question_options (id, question_id, label, content, image_path, sort_order, created_at) VALUES (?, ?, ?, ?, NULL, ?, ?)');
    $subjectLink = $pdo->prepare('INSERT INTO question_subjects (question_id, subject_id) VALUES (?, ?)');
    $correct = $pdo->prepare('UPDATE questions SET correct_option_id = ? WHERE id = ?');
    $questionIds = [];
    foreach ($questions as $index => $statement) {
        $number = $index + 1;
        $questionId = sprintf('61000000-0000-0000-0000-%012d', $number);
        $questionIds[] = $questionId;
        $questionInsert->execute([$questionId, $ids['syllabus'], $statement, $ids['user'], $now, $now]);
        foreach (['A', 'B', 'C', 'D', 'E'] as $offset => $label) {
            $optionId = sprintf('71000000-0000-0000-%04d-%012d', $number, $offset + 1);
            $content = $label === 'B' ? 'Alternativa correta para a questão de teste.' : 'Alternativa de teste para revisão visual.';
            $optionInsert->execute([$optionId, $questionId, $label, $content, $offset + 1, $now]);
            if ($label === 'B') $correct->execute([$optionId, $questionId]);
        }
        $subjectLink->execute([$questionId, $ids['subject']]);
    }

    $reviewId = '61000000-0000-0000-0000-000000000099';
    $reviewOptionId = '71000000-0000-0000-0099-000000000001';
    $pdo->prepare('INSERT INTO questions (id, syllabus_id, statement, difficulty, board, exam_year, source, reference_url, origin, status, correct_option_id, created_by, created_at, updated_at) VALUES (?, ?, ?, "MEDIUM", "Cebraspe", 2023, "Fixture E2E", NULL, "ORIGINAL", "REVIEW", NULL, ?, ?, ?)')->execute([$reviewId, $ids['syllabus'], 'Questão de revisão visual: assinale a alternativa correta sobre legalidade tributária.', $ids['user'], $now, $now]);
    $pdo->prepare('INSERT INTO question_options (id, question_id, label, content, image_path, sort_order, created_at) VALUES (?, ?, "A", ?, NULL, 1, ?)')->execute([$reviewOptionId, $reviewId, 'A lei deve definir os elementos essenciais do tributo.', $now]);
    $correct->execute([$reviewOptionId, $reviewId]);
    $pdo->prepare('INSERT INTO question_subjects (question_id, subject_id) VALUES (?, ?)')->execute([$reviewId, $ids['subject']]);

    $pdo->prepare('INSERT INTO notebooks (id, user_id, name, type, mode, status, filters, duration_seconds, started_at, finished_at, created_at, updated_at) VALUES (?, ?, ?, "PRACTICE", "STUDY", "IN_PROGRESS", ?, 132, UTC_TIMESTAMP(), NULL, ?, ?)')
        ->execute([$ids['notebook'], $ids['user'], 'Caderno visual — Direito Tributário', '{}', $now, $now]);
    $notebookQuestion = $pdo->prepare('INSERT INTO notebook_questions (notebook_id, question_id, position) VALUES (?, ?, ?)');
    foreach ($questionIds as $index => $questionId) $notebookQuestion->execute([$ids['notebook'], $questionId, $index + 1]);

    foreach ([1 => true, 2 => false] as $position => $isCorrect) {
        $attemptId = sprintf('81000000-0000-0000-0000-%012d', $position);
        $answerId = sprintf('91000000-0000-0000-0000-%012d', $position);
        $answerOption = sprintf('71000000-0000-0000-%04d-%012d', $position, $isCorrect ? 2 : 1);
        $pdo->prepare('INSERT INTO attempts (id, user_id, notebook_id, question_id, number, started_at, completed_at, context, final_answer_id, created_at) VALUES (?, ?, ?, ?, 1, ?, NULL, "STUDY", NULL, ?)')
            ->execute([$attemptId, $ids['user'], $ids['notebook'], $questionIds[$position - 1], $now, $now]);
        $pdo->prepare('INSERT INTO answers (id, attempt_id, option_id, sequence, submitted_at, elapsed_seconds, created_at) VALUES (?, ?, ?, 1, ?, ?, ?)')
            ->execute([$answerId, $attemptId, $answerOption, $now, 45 + $position, $now]);
        $pdo->prepare('UPDATE attempts SET completed_at = ?, final_answer_id = ? WHERE id = ?')->execute([$now, $answerId, $attemptId]);
    }
    $pdo->commit();
    echo "E2E fixture loaded\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Could not load E2E fixture: " . $exception->getMessage() . PHP_EOL);
    exit(1);
}
