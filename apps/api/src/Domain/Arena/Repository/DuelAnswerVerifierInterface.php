<?php declare(strict_types=1); namespace App\Domain\Arena\Repository; interface DuelAnswerVerifierInterface { public function verify(string $duelId,int $position,string $optionId):?bool; }
