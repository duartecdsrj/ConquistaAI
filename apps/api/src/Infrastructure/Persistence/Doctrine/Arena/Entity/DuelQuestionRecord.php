<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Arena\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'arena_duel_questions')]
final class DuelQuestionRecord { #[ORM\Id] #[ORM\Column(name: 'duel_id', type: 'string', length: 36)] public string $duelId; #[ORM\Id] #[ORM\Column(type: 'smallint')] public int $position; #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId; }
