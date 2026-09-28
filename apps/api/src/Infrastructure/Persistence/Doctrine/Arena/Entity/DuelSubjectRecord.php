<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Arena\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'arena_duel_subjects')]
final class DuelSubjectRecord { #[ORM\Id] #[ORM\Column(name: 'duel_id', type: 'string', length: 36)] public string $duelId; #[ORM\Id] #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId; #[ORM\Id] #[ORM\Column(name: 'taxonomy_subject_id', type: 'string', length: 36)] public string $taxonomySubjectId; }
