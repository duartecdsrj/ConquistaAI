<?php
declare(strict_types=1);
namespace App\Infrastructure\Security;
use App\Application\Identity\Port\PasswordHasherInterface;
final class NativePasswordHasher implements PasswordHasherInterface { public function verify(string $plainText,string $hash):bool{return password_verify($plainText,$hash);} public function hash(string $plainText):string{return password_hash($plainText,PASSWORD_ARGON2ID);} }
