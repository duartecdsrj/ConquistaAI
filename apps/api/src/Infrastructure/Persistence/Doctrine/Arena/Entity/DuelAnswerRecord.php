<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Arena\Entity;
use DateTimeImmutable; use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'arena_duel_answers')]
final class DuelAnswerRecord { #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id; #[ORM\Column(name: 'duel_id', type: 'string', length: 36)] public string $duelId; #[ORM\Column(type: 'smallint')] public int $position; #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId; #[ORM\Column(name: 'option_id', type: 'string', length: 36)] public string $optionId; #[ORM\Column(name: 'received_at', type: 'datetime_immutable')] public DateTimeImmutable $receivedAt; #[ORM\Column(name: 'elapsed_milliseconds', type: 'integer')] public int $elapsedMilliseconds; #[ORM\Column(name: 'is_correct', type: 'boolean', nullable: true)] public ?bool $isCorrect=null; #[ORM\Column(name: 'correct_rank', type: 'smallint', nullable: true)] public ?int $correctRank=null; #[ORM\Column(type: 'smallint')] public int $points=0; }
