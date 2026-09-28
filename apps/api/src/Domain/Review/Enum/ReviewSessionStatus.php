<?php
declare(strict_types=1);
namespace App\Domain\Review\Enum;
enum ReviewSessionStatus:string { case ACTIVE='ACTIVE'; case COMPLETED='COMPLETED'; case ABANDONED='ABANDONED'; }
