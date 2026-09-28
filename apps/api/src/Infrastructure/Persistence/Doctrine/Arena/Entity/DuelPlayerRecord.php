<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Arena\Entity;
use DateTimeImmutable; use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'arena_duel_players')]
final class DuelPlayerRecord { #[ORM\Id] #[ORM\Column(name: 'duel_id', type: 'string', length: 36)] public string $duelId; #[ORM\Id] #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId; #[ORM\Column(name: 'ready_at', type: 'datetime_immutable', nullable: true)] public ?DateTimeImmutable $readyAt=null; #[ORM\Column(name: 'joined_at', type: 'datetime_immutable')] public DateTimeImmutable $joinedAt; #[ORM\Column(type: 'integer')] public int $score=0; }
