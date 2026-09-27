<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Application\QuestionBank\Service\ProcessQuestionAuditService;
use App\Domain\QuestionBank\Service\QuestionAuditAnalyzer;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrinePublishedQuestionRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionAuditRepository;
$em = DoctrineEntityManagerFactory::create();
$service = new ProcessQuestionAuditService(
    new DoctrinePublishedQuestionRepository($em),
    new DoctrineQuestionAuditRepository($em),
    new QuestionAuditAnalyzer(),
);
fwrite(STDOUT, "Audit run: " . $service->run() . PHP_EOL);
