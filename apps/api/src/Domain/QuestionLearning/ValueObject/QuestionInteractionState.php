<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\ValueObject;
final readonly class QuestionInteractionState { public function __construct(public bool $favorite, public bool $reviewLater, public bool $notMastered) {} }
