<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Performance\DTO\Request\AppendAnswerRequestDto;
use App\Application\Performance\DTO\Request\StartAttemptRequestDto;
use App\Application\Performance\Service\AppendAnswerService;
use App\Application\Performance\Service\StartAttemptService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AttemptController
{
    public function __construct(
        private readonly AuthService $authentication,
        private readonly StartAttemptService $startAttempts,
        private readonly AppendAnswerService $appendAnswers,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function start(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
        string $notebookId,
        string $questionId,
    ): ResponseInterface {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) return $user;

        try {
            $attempt = $this->startAttempts->start(new StartAttemptRequestDto($user->id, $notebookId, $questionId));
        } catch (DomainException $exception) {
            return $this->notFound($request, $response, 'question_id', 'NOT_IN_NOTEBOOK', $exception->getMessage());
        }

        return $this->responses->success($response, [
            'id' => $attempt->id,
            'notebook_id' => $attempt->notebookId,
            'question_id' => $attempt->questionId,
            'number' => $attempt->number,
            'context' => $attempt->context,
            'started_at' => $attempt->startedAt->format(DATE_ATOM),
        ], $this->requestId($request), 201);
    }

    public function appendAnswer(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
        string $attemptId,
        ?string $optionId,
        int $elapsedSeconds,
    ): ResponseInterface {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) return $user;

        try {
            $answer = $this->appendAnswers->append(new AppendAnswerRequestDto($user->id, $attemptId, $optionId, $elapsedSeconds));
        } catch (DomainException $exception) {
            return $this->notFound($request, $response, 'attempt_id', 'RESOURCE_NOT_FOUND', $exception->getMessage());
        }

        return $this->responses->success($response, [
            'id' => $answer->id,
            'attempt_id' => $answer->attemptId,
            'option_id' => $answer->optionId,
            'sequence' => $answer->sequence,
            'elapsed_seconds' => $answer->elapsedSeconds,
            'submitted_at' => $answer->submittedAt->format(DATE_ATOM),
        ], $this->requestId($request), 201);
    }

    private function authenticatedUser(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): CurrentUserResponseDto|ResponseInterface
    {
        try {
            return $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, $this->requestId($request));
        }
    }

    private function notFound(ServerRequestInterface $request, ResponseInterface $response, string $field, string $code, string $message): ResponseInterface
    {
        return $this->responses->problem($response, 'RESOURCE_NOT_FOUND', 'Recurso nao encontrado.', 404, $this->requestId($request), [['field' => $field, 'code' => $code, 'message' => $message]]);
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
