<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Port;
use App\Domain\QuestionLearning\Entity\QuestionExplanationExecution;use App\Domain\QuestionLearning\ValueObject\QuestionExplanationContext;
interface QuestionExplanationContextReaderInterface { public function read(QuestionExplanationExecution $execution): ?QuestionExplanationContext; }
