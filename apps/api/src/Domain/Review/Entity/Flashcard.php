<?php
declare(strict_types=1);
namespace App\Domain\Review\Entity;
use App\Domain\Review\Enum\FlashcardSource;use App\Domain\Review\Enum\FlashcardType;use App\Domain\Review\ValueObject\FlashcardFingerprint;
final readonly class Flashcard { public function __construct(public string $id,public string $primaryTaxonomySubjectId,public FlashcardType $type,public string $front,public string $back,public FlashcardFingerprint $fingerprint,public FlashcardSource $source,public \DateTimeImmutable $createdAt,public \DateTimeImmutable $updatedAt,public array $taxonomySubjectIds=[]){if(trim($id)===''||trim($primaryTaxonomySubjectId)===''||trim($front)===''||trim($back)==='')throw new \InvalidArgumentException('Flashcard inválido.');} }
