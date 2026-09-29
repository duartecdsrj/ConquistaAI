<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Request;
final readonly class UpdateQuestionInteractionRequestDto { public function __construct(public string $questionId, public ?bool $favorite = null, public ?bool $reviewLater = null, public ?bool $notMastered = null) { if (trim($questionId) === '' || ($favorite === null && $reviewLater === null && $notMastered === null)) { throw new \InvalidArgumentException('Atualização de interação inválida.'); } } }
