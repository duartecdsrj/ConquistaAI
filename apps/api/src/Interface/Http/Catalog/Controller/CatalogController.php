<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog\Controller;

use App\Application\Catalog\DTO\Request\CreateExamRequestDto;
use App\Application\Catalog\DTO\Request\UpdateExamRequestDto;
use App\Application\Catalog\Service\CreateExamService;
use App\Application\Catalog\Service\DeleteExamService;
use App\Application\Catalog\Service\GetExamService;
use App\Application\Catalog\Service\ListExamsService;
use App\Application\Catalog\Service\UpdateExamService;
use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CatalogController
{
    public function __construct(
        private readonly AuthService $authentication,
        private readonly CreateExamService $createExams,
        private readonly ListExamsService $listExams,
        private readonly GetExamService $getExam,
        private readonly UpdateExamService $updateExam,
        private readonly DeleteExamService $deleteExam,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function createExam(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, CreateExamRequestDto $input): ResponseInterface
    {
        if (($authorization = $this->requireAdmin($request, $response, $access)) !== null) return $authorization;

        return $this->responses->success($response, $this->createExams->create($input), $this->requestId($request), 201);
    }

    public function listExams(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface
    {
        if (($authorization = $this->requireAuthenticated($request, $response, $access)) !== null) return $authorization;

        return $this->responses->success($response, $this->listExams->list(), $this->requestId($request));
    }

    public function getExam(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $id): ResponseInterface
    {
        if (($authorization = $this->requireAuthenticated($request, $response, $access)) !== null) return $authorization;
        $exam = $this->getExam->get($id);

        return $exam === null ? $this->notFound($request, $response) : $this->responses->success($response, $exam, $this->requestId($request));
    }

    public function updateExam(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, UpdateExamRequestDto $input): ResponseInterface
    {
        if (($authorization = $this->requireAdmin($request, $response, $access)) !== null) return $authorization;
        $exam = $this->updateExam->update($input);

        return $exam === null ? $this->notFound($request, $response) : $this->responses->success($response, $exam, $this->requestId($request));
    }

    public function deleteExam(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $id): ResponseInterface
    {
        if (($authorization = $this->requireAdmin($request, $response, $access)) !== null) return $authorization;

        return $this->deleteExam->delete($id)
            ? $this->responses->success($response, ['id' => $id, 'deleted' => true], $this->requestId($request))
            : $this->notFound($request, $response);
    }

    private function requireAdmin(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ?ResponseInterface
    {
        try {
            $user = $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->unauthenticated($request, $response);
        }

        return in_array('ADMIN', $user->roles, true)
            ? null
            : $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, $this->requestId($request));
    }

    private function requireAuthenticated(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ?ResponseInterface
    {
        try {
            $this->authentication->currentUser($access);
            return null;
        } catch (DomainException|UnavailableUserException) {
            return $this->unauthenticated($request, $response);
        }
    }

    private function unauthenticated(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, $this->requestId($request));
    }

    private function notFound(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->responses->problem($response, 'RESOURCE_NOT_FOUND', 'Recurso nao encontrado.', 404, $this->requestId($request));
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
