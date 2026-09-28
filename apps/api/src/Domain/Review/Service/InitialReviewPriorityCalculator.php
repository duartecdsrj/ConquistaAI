<?php
declare(strict_types=1);
namespace App\Domain\Review\Service;
use App\Domain\Review\Entity\UserFlashcardProgress;
final class InitialReviewPriorityCalculator implements ReviewPriorityCalculatorInterface { public function calculate(UserFlashcardProgress $p,?float $mastery,float $recent,float $relevance,\DateTimeImmutable $now):float{$late=max(0,$now->getTimestamp()-$p->state()->dueAt->getTimestamp())/86400;$gap=1-(max(0,min(100,$mastery??0))/100);return 50*min(3,$late+1)+30*$gap+15*max(0,min(1,$recent))+5*max(0,min(1,$relevance));} }
