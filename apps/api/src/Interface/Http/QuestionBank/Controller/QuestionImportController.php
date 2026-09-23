<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionBank\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
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
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function preview(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, PreviewQuestionImportRequestDto $input): ResponseInterface
    {
        try {
            $user = $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, $this->requestId($request));
        }
        if (!in_array('ADMIN', $user->roles, true)) {
            return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, $this->requestId($request));
        }

        return $this->responses->success($response, $this->preview->preview($input), $this->requestId($request), 201);
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
