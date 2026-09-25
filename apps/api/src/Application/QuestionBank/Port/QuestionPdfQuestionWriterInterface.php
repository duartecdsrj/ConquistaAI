<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Port;
interface QuestionPdfQuestionWriterInterface { /** @param list<array<string,mixed>> $questions @return array{created:int,duplicates:int,classified:int,failed:int,createdSubjects:int} */ public function write(string $createdBy,array $questions,array $pageAssets=[]):array; }
