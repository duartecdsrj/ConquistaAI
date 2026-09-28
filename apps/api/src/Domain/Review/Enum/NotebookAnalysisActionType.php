<?php
declare(strict_types=1);
namespace App\Domain\Review\Enum;
enum NotebookAnalysisActionType:string { case CREATE_OR_REUSE_CARD='CREATE_OR_REUSE_CARD'; case ADVANCE_CARD='ADVANCE_CARD'; case UPDATE_MASTERY='UPDATE_MASTERY'; case NO_ACTION='NO_ACTION'; }
