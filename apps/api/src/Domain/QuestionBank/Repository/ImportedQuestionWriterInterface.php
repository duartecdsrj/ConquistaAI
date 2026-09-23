<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
interface ImportedQuestionWriterInterface { /** @param list<array<string,mixed>> $rows */ public function createFromImport(string $syllabusId,string $createdBy,array $rows):int; }
