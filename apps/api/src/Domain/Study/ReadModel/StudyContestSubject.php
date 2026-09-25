<?php
declare(strict_types=1);
namespace App\Domain\Study\ReadModel;
final readonly class StudyContestSubject { public function __construct(public string $id,public ?string $parentId,public string $name,public int $level){} }
