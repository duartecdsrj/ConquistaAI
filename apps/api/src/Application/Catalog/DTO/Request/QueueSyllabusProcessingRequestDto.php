<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Request;
final readonly class QueueSyllabusProcessingRequestDto { public function __construct(public string $syllabusId,public bool $reprocess=false){} }
