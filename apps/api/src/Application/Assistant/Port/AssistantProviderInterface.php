<?php
declare(strict_types=1);
namespace App\Application\Assistant\Port;
use App\Domain\Assistant\ValueObject\AssistantGeneratedAnswer;use App\Domain\Assistant\ValueObject\SyllabusEvidence;
interface AssistantProviderInterface { /** @param list<SyllabusEvidence> $evidence */ public function answer(string $question,array $evidence):AssistantGeneratedAnswer; }
