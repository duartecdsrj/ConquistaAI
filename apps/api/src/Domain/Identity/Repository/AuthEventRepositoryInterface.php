<?php
declare(strict_types=1);
namespace App\Domain\Identity\Repository;
use App\Domain\Identity\Entity\AuthEvent;
interface AuthEventRepositoryInterface { public function append(AuthEvent $event): void; }
