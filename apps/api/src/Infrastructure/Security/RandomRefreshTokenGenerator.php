<?php
declare(strict_types=1);
namespace App\Infrastructure\Security;
use App\Application\Identity\Port\RefreshTokenGeneratorInterface;
final class RandomRefreshTokenGenerator implements RefreshTokenGeneratorInterface { public function generate():string{return bin2hex(random_bytes(48));} }
