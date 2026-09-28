<?php
declare(strict_types=1);
namespace App\Domain\Review\Enum;
enum ReviewRating:string { case AGAIN='AGAIN'; case HARD='HARD'; case GOOD='GOOD'; case EASY='EASY'; }
