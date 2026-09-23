<?php
declare(strict_types=1);
namespace App\Domain\Study\Enum;
enum NotebookStatus: string { case DRAFT = 'DRAFT'; case IN_PROGRESS = 'IN_PROGRESS'; case PAUSED = 'PAUSED'; case FINISHED = 'FINISHED'; }
