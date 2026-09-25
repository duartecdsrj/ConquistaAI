<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Request;
final readonly class CreateExamWithNoticeRequestDto { public function __construct(public string $name, public ?string $organizer, public ?int $year, public ?string $documentOriginalName, public ?string $documentMimeType, public ?string $documentContents) {} }
