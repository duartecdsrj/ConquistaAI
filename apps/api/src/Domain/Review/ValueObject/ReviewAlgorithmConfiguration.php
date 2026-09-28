<?php
declare(strict_types=1);
namespace App\Domain\Review\ValueObject;
final readonly class ReviewAlgorithmConfiguration { public function __construct(public int $againMinutes=10,public int $hardDays=1,public int $goodDays=3,public int $easyDays=7,public int $minimumEvidence=10){if(min($againMinutes,$hardDays,$goodDays,$easyDays,$minimumEvidence)<1)throw new \InvalidArgumentException('Configuração de revisão inválida.');} }
