<?php
declare(strict_types=1);
namespace App\Application\Performance\Service;
use App\Application\Performance\DTO\Request\AppendAnswerRequestDto;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
final class AppendAnswerService { public function __construct(private readonly AttemptRepositoryInterface $attempts) {} public function append(AppendAnswerRequestDto $request): Answer { $answer = new Answer($this->uuid(), $request->attemptId, $request->optionId, $request->sequence, $request->elapsedSeconds, new \DateTimeImmutable("now", new \DateTimeZone("UTC"))); $this->attempts->appendAnswer($answer); return $answer; } private function uuid(): string { $bytes=random_bytes(16); $bytes[6]=chr((ord($bytes[6])&15)|64); $bytes[8]=chr((ord($bytes[8])&63)|128); return vsprintf("%s%s-%s-%s-%s-%s%s%s",str_split(bin2hex($bytes),4)); } }
