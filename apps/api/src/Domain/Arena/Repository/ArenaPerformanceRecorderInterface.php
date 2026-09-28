<?php declare(strict_types=1);namespace App\Domain\Arena\Repository;interface ArenaPerformanceRecorderInterface{public function record(string $duelId,int $position):void;}
