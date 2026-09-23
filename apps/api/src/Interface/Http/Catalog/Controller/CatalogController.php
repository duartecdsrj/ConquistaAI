<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog\Controller;

use App\Application\Catalog\DTO\Request\CreateExamRequestDto;
use App\Application\Catalog\Service\CreateExamService;
use App\Application\Catalog\Service\ListExamsService;
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
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function createExam(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, CreateExamRequestDto $input): ResponseInterface
    {
        $authorization = $this->requireAdmin($request, $response, $access);
        if ($authorization !== null) {
            return $authorization;
        }

        return $this->responses->success($response, $this->createExams->create($input), $this->requestId($request), 201);
    }

    public function listExams(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface
    {
        $authorization = $this->requireAuthenticated($request, $response, $access);
        if ($authorization !== null) {
            return $authorization;
        }

        return $this->responses->success($response, $this->listExams->list(), $this->requestId($request));
    }

    private function requireAdmin(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ?ResponseInterface
    {
        try {
            $user = $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->unauthenticated($request, $response);
        }

        if (!in_array('ADMIN', $user->roles, true)) {
            return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, $this->requestId($request));
        }

        return null;
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

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
