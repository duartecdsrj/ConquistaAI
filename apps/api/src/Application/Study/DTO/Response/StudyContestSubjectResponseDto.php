<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Response;
final readonly class StudyContestSubjectResponseDto { public function __construct(public string $id,public ?string $parentId,public string $name,public int $level){} }
