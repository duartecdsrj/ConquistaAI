<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Performance\DTO\Request\StartAttemptRequestDto;
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
        try {
            $user = $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, $this->requestId($request));
        }

        try {
            $attempt = $this->startAttempts->start(new StartAttemptRequestDto($user->id, $notebookId, $questionId));
        } catch (DomainException $exception) {
            return $this->responses->problem(
                $response,
                'RESOURCE_NOT_FOUND',
                'Recurso nao encontrado.',
                404,
                $this->requestId($request),
                [['field' => 'question_id', 'code' => 'NOT_IN_NOTEBOOK', 'message' => $exception->getMessage()]],
            );
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

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
