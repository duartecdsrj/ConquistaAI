<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\Flashcard;use App\Domain\Review\ValueObject\FlashcardFingerprint;
interface FlashcardRepositoryInterface { public function findByFingerprint(string $taxonomySubjectId,FlashcardFingerprint $fingerprint):?Flashcard; public function findById(string $id):?Flashcard; public function save(Flashcard $flashcard):void; }
