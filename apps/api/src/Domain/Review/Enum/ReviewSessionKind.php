<?php
declare(strict_types=1);
namespace App\Domain\Review\Enum;
enum ReviewSessionKind:string { case DAILY='DAILY'; case QUICK='QUICK'; }
