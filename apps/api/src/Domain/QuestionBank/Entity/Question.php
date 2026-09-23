<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Entity;
final class Question {
    /** @var list<QuestionOption> */ private array $options;
    public function __construct(private readonly string $id, private string $statement, private string $status, array $options, private ?string $correctOptionId) { $this->options=array_values($options); $this->assertOptions(); }
    public function publish(): void { if (trim($this->statement)==='' || $this->correctOptionId===null || !$this->hasOption($this->correctOptionId)) { throw new \DomainException('A published question requires a valid answer key.'); } $this->status='PUBLISHED'; }
    public function isPublished(): bool { return $this->status==='PUBLISHED'; }
    private function assertOptions(): void { if (count($this->options)<2 || count(array_unique(array_map(static fn(QuestionOption $option): string=>$option->label,$this->options)))!==count($this->options)) { throw new \DomainException('Question options must be unique.'); } }
    private function hasOption(string $id): bool { foreach($this->options as $option){if($option->id===$id){return true;}}return false; }
}
