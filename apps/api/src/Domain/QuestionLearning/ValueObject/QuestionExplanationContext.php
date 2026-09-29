<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\ValueObject;
final readonly class QuestionExplanationContext { /** @param list<array{id:string,label:string,content:string}> $options @param list<string> $taxonomySubjectIds */ public function __construct(public string $questionId,public string $statement,public array $options,public array $taxonomySubjectIds,public string $safetyMode,public ?string $selectedOptionId=null,public ?bool $correct=null,public bool $notMastered=false) {} }
