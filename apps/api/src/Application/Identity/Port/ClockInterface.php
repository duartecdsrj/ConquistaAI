<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
interface ClockInterface { public function now(): \DateTimeImmutable; }
