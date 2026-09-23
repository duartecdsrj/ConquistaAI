<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
interface AccessTokenVerifierInterface { public function verify(string $accessToken): AccessTokenClaims; }
