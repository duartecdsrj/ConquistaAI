<?php
declare(strict_types=1);
namespace App\Domain\Review\Enum;
enum FlashcardSource:string { case SYSTEM='SYSTEM'; case NOTEBOOK_ANALYSIS='NOTEBOOK_ANALYSIS'; case EDITORIAL='EDITORIAL'; }
