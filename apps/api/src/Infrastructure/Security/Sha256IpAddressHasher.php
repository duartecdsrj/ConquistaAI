<?php
declare(strict_types=1);
namespace App\Infrastructure\Security;
use App\Application\Identity\Port\IpAddressHasherInterface;
final class Sha256IpAddressHasher implements IpAddressHasherInterface { public function hash(string $ipAddress):string{return hash('sha256',$ipAddress);} }
