<?php
declare(strict_types=1);

namespace App\Interface\Http\Study\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Study\DTO\Request\CreateNotebookInputRequestDto;
use App\Application\Study\DTO\Request\CreateNotebookRequestDto;
use App\Application\Study\DTO\Request\GetNotebookRequestDto;
use App\Application\Study\DTO\Request\ListNotebooksRequestDto;
use App\Application\Study\Service\CreateNotebookService;
use App\Application\Study\Service\GetNotebookService;
use App\Application\Study\Service\ListNotebooksService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StudyController
{
    public function __construct(
        private readonly AuthService $authentication,
        private readonly CreateNotebookService $createNotebook,
        private readonly GetNotebookService $getNotebook,
        private readonly ListNotebooksService $listNotebooks,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function getNotebook(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
        GetNotebookRequestDto $input,
    ): ResponseInterface {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) {
            return $user;
        }

        $notebook = $this->getNotebook->getForUser($user->id, $input);
        if ($notebook === null) {
            return $this->responses->problem(
                $response,
                'RESOURCE_NOT_FOUND',
                'Recurso nao encontrado.',
                404,
                $this->requestId($request),
            );
        }

        return $this->responses->success($response, $notebook, $this->requestId($request));
    }

    public function listNotebooks(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
        ListNotebooksRequestDto $input,
    ): ResponseInterface {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) {
            return $user;
        }

        $page = $this->listNotebooks->listForUser($user->id, $input);

        return $this->responses->paginated(
            $response,
            $page->items,
            $this->requestId($request),
            $page->page,
            $page->perPage,
            $page->total,
        );
    }

    public function createNotebook(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
        CreateNotebookInputRequestDto $input,
    ): ResponseInterface {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) {
            return $user;
        }

        $command = new CreateNotebookRequestDto(
            $user->id,
            $input->name,
            $input->mode,
            $input->quantity,
            $input->questionIds,
        );

        try {
            $notebook = $this->createNotebook->create($command);
        } catch (DomainException|\InvalidArgumentException $exception) {
            return $this->responses->problem(
                $response,
                'VALIDATION_FAILED',
                'Um ou mais campos sao invalidos.',
                422,
                $this->requestId($request),
                [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $exception->getMessage()]],
            );
        }

        return $this->responses->success($response, $notebook, $this->requestId($request), 201);
    }

    private function authenticatedUser(
        ServerRequestInterface $request,
        ResponseInterface $response,
        AccessTokenRequestDto $access,
    ): CurrentUserResponseDto|ResponseInterface {
        try {
            return $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem(
                $response,
                'UNAUTHENTICATED',
                'Credenciais invalidas ou expiradas.',
                401,
                $this->requestId($request),
            );
        }
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
