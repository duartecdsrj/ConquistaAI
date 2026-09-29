<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_import_image_anchors')]
class QuestionImportImageAnchorRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'analysis_id', type: 'string', length: 36)] public string $analysisId;
    #[ORM\Column(name: 'target_kind', type: 'string', length: 16)] public string $targetKind;
    #[ORM\Column(name: 'option_position', type: 'smallint', nullable: true)] public ?int $optionPosition = null;
    #[ORM\Column(name: 'source_page', type: 'integer')] public int $sourcePage;
    #[ORM\Column(name: 'source_asset_index', type: 'integer', nullable: true)] public ?int $sourceAssetIndex = null;
    #[ORM\Column(type: 'string', length: 32)] public string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
