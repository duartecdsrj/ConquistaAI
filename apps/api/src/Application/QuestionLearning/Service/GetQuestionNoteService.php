<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Service;
use App\Application\QuestionLearning\DTO\Response\QuestionNoteResponseDto;use App\Application\QuestionLearning\Mapper\QuestionNoteResponseMapper;use App\Domain\QuestionBank\Repository\FrozenQuestionReaderInterface;use App\Domain\QuestionLearning\Repository\QuestionNoteRepositoryInterface;
final readonly class GetQuestionNoteService { public function __construct(private FrozenQuestionReaderInterface $questions, private QuestionNoteRepositoryInterface $notes, private QuestionNoteResponseMapper $mapper) {} public function execute(string $userId, string $questionId): ?QuestionNoteResponseDto { if (count($this->questions->findByIds([$questionId])) !== 1) { throw new \DomainException('Questão não encontrada.'); } $note = $this->notes->findForUserQuestion($userId, $questionId); return $note === null ? null : $this->mapper->toResponse($note); } }
