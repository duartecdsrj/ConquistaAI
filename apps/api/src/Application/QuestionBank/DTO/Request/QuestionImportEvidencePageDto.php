<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Request;

final readonly class QuestionImportEvidencePageDto
{
    public function __construct(public int $pageNumber, public string $content)
    {
        if ($pageNumber < 1 || trim($content) === '') throw new \InvalidArgumentException('Página de evidência inválida.');
    }
}
