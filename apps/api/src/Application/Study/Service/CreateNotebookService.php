<?php
declare(strict_types=1);
namespace App\Application\Study\Service;
use App\Application\Study\DTO\Request\CreateNotebookRequestDto;
use App\Application\Study\DTO\Response\NotebookResponseDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
final class CreateNotebookService {
    public function __construct(private readonly NotebookRepositoryInterface $notebooks, private readonly NotebookResponseMapper $mapper, private readonly \DateTimeZone $utc = new \DateTimeZone('UTC')) {}
    public function create(CreateNotebookRequestDto $request): NotebookResponseDto {
        $selection = FrozenQuestionSelection::fromQuestionIds($request->questionIds, $request->quantity);
        $notebook = Notebook::create($request->userId,$request->name,NotebookMode::from($request->mode),$selection,new \DateTimeImmutable('now',$this->utc));
        $this->notebooks->save($notebook);
        return $this->mapper->toResponse($notebook);
    }
}
