<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Request;
use App\Domain\QuestionLearning\Enum\ProblemReportCategory;
final readonly class CreateQuestionProblemReportRequestDto { public function __construct(public string $questionId, public ProblemReportCategory $category, public string $description) { if (trim($questionId) === '' || mb_strlen(trim($description)) < 3 || mb_strlen($description) > 2000) { throw new \InvalidArgumentException('Relato inválido.'); } } }
