<?php
declare(strict_types=1);

namespace App\Interface\Http\Study\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Application\Study\DTO\Request\UpdateStudyGoalRequestDto;
use App\Application\Study\Service\GetStudyGoalService;
use App\Application\Study\Service\UpdateStudyGoalService;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StudyGoalController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly GetStudyGoalService $get,
        private readonly UpdateStudyGoalService $update,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface
    {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) return $user;
        return $this->responses->success($response, $this->get->getForUser($user->id), (string) $request->getAttribute('request_id'));
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, UpdateStudyGoalRequestDto $input): ResponseInterface
    {
        $user = $this->authenticatedUser($request, $response, $access);
        if ($user instanceof ResponseInterface) return $user;
        return $this->responses->success($response, $this->update->updateForUser($user->id, $input), (string) $request->getAttribute('request_id'));
    }

    private function authenticatedUser(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): CurrentUserResponseDto|ResponseInterface
    {
        try {
            return $this->auth->currentUser($access);
        } catch (DomainException|UnavailableUserException) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id'));
        }
    }
}
