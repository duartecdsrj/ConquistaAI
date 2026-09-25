<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Response;
final readonly class CreateExamWithNoticeResponseDto { public function __construct(public ExamResponseDto $exam, public ?SyllabusResponseDto $syllabus, public ?SyllabusProcessingJobResponseDto $processingJob) {} }
