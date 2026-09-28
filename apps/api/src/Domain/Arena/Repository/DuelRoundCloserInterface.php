<?php declare(strict_types=1);namespace App\Domain\Arena\Repository;interface DuelRoundCloserInterface{public function closeIfReady(string $duelId,int $position,\DateTimeImmutable $deadline):bool;}
