<?php
declare(strict_types=1);
namespace App\Domain\Identity\Entity;
final readonly class AuthSession {
 public function __construct(public string $id,public string $userId,public string $familyId,public string $refreshTokenHash,public \DateTimeImmutable $expiresAt,public ?\DateTimeImmutable $revokedAt,public ?string $deviceName) {}
 public static function create(string $userId,string $hash,\DateTimeImmutable $expiresAt,?string $familyId,?string $deviceName): self { $id=self::uuid(); return new self($id,$userId,$familyId??$id,$hash,$expiresAt,null,$deviceName); }
 public function isRevoked(): bool { return $this->revokedAt !== null; }
 public function isExpiredAt(\DateTimeImmutable $now): bool { return $this->expiresAt <= $now; }
 private static function uuid(): string { $b=random_bytes(16);$b[6]=chr((ord($b[6])&15)|64);$b[8]=chr((ord($b[8])&63)|128);return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($b),4)); }
}
