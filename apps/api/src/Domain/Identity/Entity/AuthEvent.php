<?php
declare(strict_types=1);
namespace App\Domain\Identity\Entity;
final readonly class AuthEvent { private function __construct(public ?string $userId,public string $event,public string $ipHash,public \DateTimeImmutable $occurredAt) {} public static function loginFailed(?string $id,string $ipHash,\DateTimeImmutable $at):self{return new self($id,'LOGIN_FAILED',$ipHash,$at);} public static function loginSucceeded(string $id,string $ipHash,\DateTimeImmutable $at):self{return new self($id,'LOGIN_SUCCEEDED',$ipHash,$at);} }
