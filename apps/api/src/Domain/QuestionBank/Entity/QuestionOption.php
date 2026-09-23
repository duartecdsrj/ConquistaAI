<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Entity;
final readonly class QuestionOption { public function __construct(public string $id, public string $label, public string $content, public int $sortOrder) { if (!preg_match('/^[A-E]$/',$label) || trim($content)==='' || $sortOrder < 1) { throw new \DomainException('Invalid question option.'); } } }
