<?php
declare(strict_types=1);
namespace App\Application\Study\Mapper;
use App\Application\Study\DTO\Response\NotebookResponseDto;
use App\Domain\Study\Entity\Notebook;
final class NotebookResponseMapper { public function toResponse(Notebook $notebook): NotebookResponseDto { return new NotebookResponseDto($notebook->id,$notebook->name,$notebook->mode->value,$notebook->selection->questionIds,$notebook->createdAt->format(DATE_ATOM)); } }
