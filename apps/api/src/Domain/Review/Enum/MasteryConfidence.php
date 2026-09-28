<?php
declare(strict_types=1);
namespace App\Domain\Review\Enum;
enum MasteryConfidence:string { case INSUFFICIENT='INSUFFICIENT'; case LOW='LOW'; case MEDIUM='MEDIUM'; case HIGH='HIGH'; }
