<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog\Controller;

use App\Application\Catalog\DTO\Request\AssignPositionTaxonomySubjectsRequestDto;
use App\Application\Catalog\Service\AssignPositionTaxonomySubjectsService;
use App\Application\Catalog\Service\ListPositionTaxonomySubjectsService;
use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PositionTaxonomyAssignmentController
{
    public function __construct(private readonly AuthService $auth, private readonly AssignPositionTaxonomySubjectsService $assignments, private readonly ListPositionTaxonomySubjectsService $subjects, private readonly ApiResponseFactory $responses) {}

    public function assign(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, AssignPositionTaxonomySubjectsRequestDto $input): ResponseInterface
    {
        if (($failure = $this->admin($request, $response, $access)) !== null) return $failure;
        try { return $this->responses->success($response, $this->assignments->assign($input), $this->requestId($request)); }
        catch (\DomainException|\InvalidArgumentException) { return $this->responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos são inválidos.', 422, $this->requestId($request)); }
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $positionId): ResponseInterface
    {
        if (($failure = $this->authenticated($request, $response, $access)) !== null) return $failure;
        try { return $this->responses->success($response, $this->subjects->list($positionId), $this->requestId($request)); }
        catch (\DomainException|\InvalidArgumentException) { return $this->responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos são inválidos.', 422, $this->requestId($request)); }
    }

    private function authenticated(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ?ResponseInterface
    {
        try { $this->auth->currentUser($access); return null; }
        catch (\DomainException|UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais inválidas ou expiradas.', 401, $this->requestId($request)); }
    }
    private function admin(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ?ResponseInterface
    {
        try { $user = $this->auth->currentUser($access); }
        catch (\DomainException|UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais inválidas ou expiradas.', 401, $this->requestId($request)); }
        return in_array('ADMIN', $user->roles, true) ? null : $this->responses->problem($response, 'FORBIDDEN', 'Permissão insuficiente.', 403, $this->requestId($request));
    }
    private function requestId(ServerRequestInterface $request): string { return (string) $request->getAttribute('request_id'); }
}
