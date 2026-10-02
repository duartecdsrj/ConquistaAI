<?php
declare(strict_types=1);
namespace App\Domain\Review\Entity;
use App\Domain\Review\Enum\ReviewSessionKind;use App\Domain\Review\Enum\ReviewSessionStatus;
final class ReviewSession { public function __construct(public readonly string $id,public readonly string $userId,public readonly ReviewSessionKind $kind,public ReviewSessionStatus $status,public readonly int $requestedLimit,public readonly \DateTimeImmutable $createdAt,public ?\DateTimeImmutable $completedAt=null,public array $flashcardIds=[],public array $cards=[],public int $currentPosition=0){if($requestedLimit<1||$requestedLimit>100)throw new \InvalidArgumentException('Limite de sessão inválido.');} public function currentId():?string{return $this->currentPosition>0?($this->flashcardIds[$this->currentPosition-1]??null):null;} public function complete(\DateTimeImmutable $now):void{$this->status=ReviewSessionStatus::COMPLETED;$this->completedAt=$now;} }
