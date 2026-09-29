<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ValueObject;

use App\Domain\QuestionBank\Enum\QuestionImportImageAnchorStatus;
use App\Domain\QuestionBank\Enum\QuestionImportImageTarget;

final readonly class QuestionImportImageAnchor
{
    public function __construct(
        public string $id,
        public QuestionImportImageTarget $target,
        public ?int $optionPosition,
        public int $sourcePage,
        public ?int $sourceAssetIndex,
        public QuestionImportImageAnchorStatus $status,
    ) {
        if ($sourcePage < 1 || ($sourceAssetIndex !== null && $sourceAssetIndex < 0)) throw new \InvalidArgumentException('Âncora de imagem inválida.');
        if (($target === QuestionImportImageTarget::OPTION) !== ($optionPosition !== null)) throw new \InvalidArgumentException('Destino de imagem inválido.');
    }
}
