<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
interface IpAddressHasherInterface { public function hash(string $ipAddress): string; }
