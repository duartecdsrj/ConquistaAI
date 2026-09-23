<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Request;

final readonly class PreviewQuestionImportRequestDto
{
    public function __construct(public string $format, public string $content)
    {
    }
}
