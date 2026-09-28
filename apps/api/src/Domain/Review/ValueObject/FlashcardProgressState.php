<?php
declare(strict_types=1);
namespace App\Domain\Review\ValueObject;
final readonly class FlashcardProgressState { public function __construct(public \DateTimeImmutable $dueAt,public int $intervalDays,public float $easeFactor,public int $repetitions,public int $lapses){if($intervalDays<0||$repetitions<0||$lapses<0||$easeFactor<1.3)throw new \InvalidArgumentException('Estado de revisão inválido.');} public function toArray():array{return ['dueAt'=>$this->dueAt->format(DATE_ATOM),'intervalDays'=>$this->intervalDays,'easeFactor'=>$this->easeFactor,'repetitions'=>$this->repetitions,'lapses'=>$this->lapses];} }
