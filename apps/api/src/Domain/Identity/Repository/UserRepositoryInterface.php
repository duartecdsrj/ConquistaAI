<?php
declare(strict_types=1);
namespace App\Domain\Identity\Repository;
use App\Domain\Identity\Entity\User;
interface UserRepositoryInterface { public function findByEmail(string $email): ?User; public function findById(string $id): ?User; }
