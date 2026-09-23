<?php
declare(strict_types=1);

namespace App\Application\Study\Service;

use App\Application\Study\DTO\Request\ListNotebooksRequestDto;
use App\Application\Study\DTO\Response\PaginatedNotebooksResponseDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Domain\Study\Repository\NotebookRepositoryInterface;

final class ListNotebooksService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly NotebookResponseMapper $mapper,
    ) {
    }

    public function listForUser(
        string $userId,
        ListNotebooksRequestDto $request,
    ): PaginatedNotebooksResponseDto {
        $notebooks = $this->notebooks->listForUser($userId, $request->offset(), $request->perPage);

        return new PaginatedNotebooksResponseDto(
            array_map($this->mapper->toResponse(...), $notebooks),
            $request->page,
            $request->perPage,
            $this->notebooks->countForUser($userId),
        );
    }
}
