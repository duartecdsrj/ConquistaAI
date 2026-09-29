<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Request;

final readonly class QuestionImportImageManifestItemDto
{
    public function __construct(public int $pageNumber, public int $assetIndex, public string $assetPath)
    {
        if ($pageNumber < 1 || $assetIndex < 0 || trim($assetPath) === '') throw new \InvalidArgumentException('Manifesto de imagem inválido.');
    }
}
