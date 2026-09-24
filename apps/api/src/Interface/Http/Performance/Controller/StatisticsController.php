<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Performance\Service\GetBasicStatisticsService;
use App\Application\Performance\DTO\Request\GetSyllabusDashboardRequestDto;
use App\Application\Performance\Service\GetSyllabusDashboardService;
use App\Application\Performance\Service\GetStudyPlanService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StatisticsController
{
    public function __construct(
        private readonly AuthService $authentication,
        private readonly GetStudyPlanService $studyPlan,
        private readonly GetSyllabusDashboardService $dashboard,
        private readonly GetBasicStatisticsService $statistics,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function mine(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface
    {
        try {
            $user = $this->authentication->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id'));
        }


        return $this->responses->success($response, $this->statistics->getForUser($user->id), (string) $request->getAttribute('request_id'));
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, GetSyllabusDashboardRequestDto $input): ResponseInterface
    {
        try { $user = $this->authentication->currentUser($access); }
        catch (DomainException|UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
        return $this->responses->success($response, $this->dashboard->getForUser($user->id, $input), (string) $request->getAttribute('request_id'));

    }
    public function studyPlan(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface
    {
        try { $user = $this->authentication->currentUser($access); }
        catch (DomainException|UnavailableUserException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
        return $this->responses->success($response, $this->studyPlan->getForUser($user->id), (string) $request->getAttribute('request_id'));
    }
}
