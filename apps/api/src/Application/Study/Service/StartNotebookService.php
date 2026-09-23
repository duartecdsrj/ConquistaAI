<?php
declare(strict_types=1);

namespace App\Application\Study\Service;

use App\Application\Study\DTO\Request\GetNotebookRequestDto;
use App\Application\Study\DTO\Response\NotebookResponseDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Port\TransactionManagerInterface;
use App\Domain\Study\Repository\NotebookRepositoryInterface;

final class StartNotebookService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly NotebookResponseMapper $mapper,
        private readonly TransactionManagerInterface $transactions,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function startForUser(string $userId, GetNotebookRequestDto $request): ?NotebookResponseDto
    {
        return $this->transactions->transactional(function () use ($userId, $request): ?NotebookResponseDto {
            $notebook = $this->notebooks->findByIdForUser($request->id, $userId);
            if ($notebook === null) {
                return null;
            }

            $notebook->start(new \DateTimeImmutable('now', $this->utc));
            $this->notebooks->save($notebook);

            return $this->mapper->toResponse($notebook);
        });
    }
}
