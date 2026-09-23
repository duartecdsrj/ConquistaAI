<?php
declare(strict_types=1);

namespace App\Application\Study\Service;

use App\Application\Study\DTO\Request\GetNotebookRequestDto;
use App\Application\Study\DTO\Response\NotebookResponseDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Domain\Study\Repository\NotebookRepositoryInterface;

final class GetNotebookService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly NotebookResponseMapper $mapper,
    ) {
    }

    public function getForUser(string $userId, GetNotebookRequestDto $request): ?NotebookResponseDto
    {
        $notebook = $this->notebooks->findByIdForUser($request->id, $userId);

        return $notebook === null ? null : $this->mapper->toResponse($notebook);
    }
}
