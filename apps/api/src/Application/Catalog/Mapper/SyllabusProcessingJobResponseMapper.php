<?php
declare(strict_types=1);
namespace App\Application\Catalog\Mapper;
use App\Application\Catalog\DTO\Response\SyllabusProcessingJobResponseDto;use App\Domain\Catalog\Entity\SyllabusProcessingJob;
final class SyllabusProcessingJobResponseMapper { public function map(SyllabusProcessingJob $job):SyllabusProcessingJobResponseDto{return new SyllabusProcessingJobResponseDto($job->id,$job->syllabusId,$job->documentSha256,$job->status,$job->progress,$job->errorMessage,$job->createdAt->format(DATE_ATOM),$job->startedAt?->format(DATE_ATOM),$job->finishedAt?->format(DATE_ATOM));} }
