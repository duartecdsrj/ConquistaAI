<?php
declare(strict_types=1);
namespace App\Infrastructure\Security;
use App\Application\Identity\Port\ClockInterface;
final class SystemClock implements ClockInterface { public function now():\DateTimeImmutable{return new \DateTimeImmutable('now',new \DateTimeZone('UTC'));} }
