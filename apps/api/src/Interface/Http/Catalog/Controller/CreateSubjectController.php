<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog\Controller;

use App\Application\Catalog\DTO\Request\CreateSubjectRequestDto;
use App\Application\Catalog\Service\CreateSubjectService;
use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CreateSubjectController
{
    public function __construct(private readonly AuthService $auth, private readonly CreateSubjectService $service, private readonly ApiResponseFactory $responses) {}
    public function create(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, CreateSubjectRequestDto $input): ResponseInterface
    {
        try {
            if (!in_array('ADMIN', $this->auth->currentUser($access)->roles, true)) return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, (string) $request->getAttribute('request_id'));
            return $this->responses->success($response, $this->service->create($input), (string) $request->getAttribute('request_id'), 201);
        } catch (\DomainException|\InvalidArgumentException) { return $this->responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id')); }
        catch (UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
    }
}
