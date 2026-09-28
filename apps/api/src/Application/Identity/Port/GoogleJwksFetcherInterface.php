<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
/** @phpstan-type Jwk array{kid:string,n:string,e:string} */
interface GoogleJwksFetcherInterface { /** @return list<array{kid:string,n:string,e:string}> */ public function fetch(): array; }
