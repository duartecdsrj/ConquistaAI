<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog\Controller;

use App\Application\Catalog\DTO\Request\AssignSubjectTaxonomySubjectsRequestDto;
use App\Application\Catalog\Service\AssignSubjectTaxonomySubjectsService;
use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SubjectTaxonomyAssignmentController
{
    public function __construct(private readonly AuthService $auth, private readonly AssignSubjectTaxonomySubjectsService $service, private readonly ApiResponseFactory $responses) {}
    public function assign(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, AssignSubjectTaxonomySubjectsRequestDto $input): ResponseInterface
    {
        try {
            $user = $this->auth->currentUser($access);
            if (!in_array('ADMIN', $user->roles, true)) return $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, (string) $request->getAttribute('request_id'));
            return $this->responses->success($response, $this->service->assign($input), (string) $request->getAttribute('request_id'));
        } catch (\DomainException|\InvalidArgumentException) { return $this->responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id')); }
        catch (UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
    }
}
