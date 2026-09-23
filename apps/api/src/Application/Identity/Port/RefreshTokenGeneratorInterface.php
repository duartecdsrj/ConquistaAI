<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
interface RefreshTokenGeneratorInterface { public function generate(): string; }
