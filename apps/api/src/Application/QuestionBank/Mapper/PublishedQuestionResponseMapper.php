<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Mapper;

use App\Application\QuestionBank\DTO\Response\PublishedQuestionOptionResponseDto;
use App\Application\QuestionBank\DTO\Response\PublishedQuestionResponseDto;
use App\Domain\QuestionBank\ReadModel\PublishedQuestion;

final class PublishedQuestionResponseMapper
{
    public function toResponse(PublishedQuestion $question): PublishedQuestionResponseDto
    {
        return new PublishedQuestionResponseDto(
            $question->id,
            $question->statement,
            $question->difficulty,
            $question->board,
            $question->year,
            array_map(
                static fn ($option): PublishedQuestionOptionResponseDto => new PublishedQuestionOptionResponseDto(
                    $option->id,
                    $option->label,
                    $option->content,
                    $option->position,
                    $option->assetUrls,
                ),
                $question->options,
            ),
            $question->taxonomySubjectIds,
            $question->status,
            $question->source,
            $question->assetUrls,
        );
    }
}
