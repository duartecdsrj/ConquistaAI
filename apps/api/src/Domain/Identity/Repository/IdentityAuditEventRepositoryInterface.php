<?php
declare(strict_types=1);
namespace App\Domain\Identity\Repository;
use App\Domain\Identity\Entity\IdentityAuditEvent;
interface IdentityAuditEventRepositoryInterface { public function append(IdentityAuditEvent $event): void; }
