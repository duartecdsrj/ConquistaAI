<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionBank\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
use App\Application\QuestionBank\DTO\Request\CommitQuestionImportRequestDto;
use App\Application\QuestionBank\Service\CommitQuestionImportService;
use App\Application\QuestionBank\Service\GetQuestionImportService;
use App\Application\QuestionBank\Service\PreviewQuestionImportService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class QuestionImportController
{
    public function __construct(
        private readonly AuthService $authentication,
        private readonly PreviewQuestionImportService $preview,
        private readonly GetQuestionImportService $getImport,
        private readonly CommitQuestionImportService $commitImport,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function preview(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, PreviewQuestionImportRequestDto $input): ResponseInterface
    {
        $user = $this->admin($request, $response, $access);
        if ($user instanceof ResponseInterface) {
            return $user;
        }

        $report = $this->preview->preview(new PreviewQuestionImportRequestDto($input->format, $input->content, $user->id));

        return $this->responses->success($response, $report, $this->requestId($request), 201);
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $id): ResponseInterface
    {
        $user = $this->admin($request, $response, $access);
        if ($user instanceof ResponseInterface) {
            return $user;
        }
        $report = $this->getImport->getForUser($id, $user->id);

        return $report === null
            ? $this->responses->problem($response, 'RESOURCE_NOT_FOUND', 'Recurso nao encontrado.', 404, $this->requestId($request))
            : $this->responses->success($response, $report, $this->requestId($request));
    }

    public function commit(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, CommitQuestionImportRequestDto $input): ResponseInterface
    {
        $user = $this->admin($request, $response, $access);
        if ($user instanceof ResponseInterface) { return $user; }
        try { $result = $this->commitImport->commit($input); } catch (DomainException $exception) { return $this->responses->problem($response, "STATE_CONFLICT", "A importacao nao pode ser confirmada.", 409, $this->requestId($request)); }
        return $this->responses->success($response, $result, $this->requestId($request), 201);
    }

    private function admin(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): \App\Application\Identity\DTO\Response\CurrentUserResponseDto|ResponseInterface
    {
        try {
            $user = $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, $this->requestId($request));
        }
        if (!in_array('ADMIN', $user->roles, true)) {
            return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, $this->requestId($request));
        }

        return $user;
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
