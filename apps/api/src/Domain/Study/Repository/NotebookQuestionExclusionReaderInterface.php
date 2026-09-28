<?php
declare(strict_types=1);
namespace App\Domain\Study\Repository;
interface NotebookQuestionExclusionReaderInterface { /** @return list<string> */ public function excludedQuestionIds(string $userId,\DateTimeImmutable $since):array; }
