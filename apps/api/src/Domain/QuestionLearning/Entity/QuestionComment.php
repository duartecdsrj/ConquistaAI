<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Entity;
final readonly class QuestionComment { public function __construct(public string $id, public string $questionId, public string $authorUserId, public ?string $parentId, public string $content, public \DateTimeImmutable $createdAt, public \DateTimeImmutable $updatedAt) { if (mb_strlen(trim($content)) < 1 || mb_strlen($content) > 2000) { throw new \DomainException('Comentário inválido.'); } } }
