<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_quality_signals')]
class QuestionQualitySignalRecord
{
    #[ORM\Id] #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(name: 'analysis_id', type: 'string', length: 36)] public string $analysisId;
    #[ORM\Column(type: 'string', length: 64)] public string $category;
    #[ORM\Column(name: 'safe_message', type: 'string', length: 500)] public string $safeMessage;
    #[ORM\Column(name: 'detected_at', type: 'datetime_immutable')] public DateTimeImmutable $detectedAt;
}
