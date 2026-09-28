<?php
declare(strict_types=1);

namespace App\Interface\Http\Identity\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\DTO\Request\CreateAdminUserRequestDto;
use App\Application\Identity\DTO\Request\UpdateUserRolesRequestDto;
use App\Application\Identity\DTO\Request\UpdateUserStatusRequestDto;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Application\Identity\Service\AuthService;
use App\Application\Identity\Service\UserAdministrationService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Interface\Http\Identity\UserAdministrationRequestFactory;
use DomainException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class UserAdministrationController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly UserAdministrationService $users,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): ResponseInterface
    {
        $administrator = $this->administrator($request, $response, $access);
        if ($administrator instanceof ResponseInterface) {
            return $administrator;
        }

        $query = $request->getQueryParams();
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($query['per_page'] ?? 25)));
        $result = $this->users->list(
            $page,
            $perPage,
            is_string($query['query'] ?? null) ? $query['query'] : null,
            is_string($query['status'] ?? null) ? $query['status'] : null,
        );

        return $this->responses->paginated($response, $result['items'], $this->requestId($request), $page, $perPage, $result['total']);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, UserAdministrationRequestFactory $factory): ResponseInterface
    {
        return $this->run($request, $response, $access, function (CurrentUserResponseDto $administrator) use ($request, $factory) {
            $body = $factory->body($request);

            return $this->users->create(new CreateAdminUserRequestDto(
                $administrator->id,
                $factory->requiredString($body, 'name'),
                $factory->requiredString($body, 'email'),
                $factory->optionalString($body, 'password'),
                $factory->roles($body),
                $factory->requiredString($body, 'status'),
                $factory->optionalString($body, 'google_email'),
            ));
        }, 201);
    }

    public function setGoogle(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $userId, UserAdministrationRequestFactory $factory): ResponseInterface
    {
        return $this->run($request, $response, $access, function (CurrentUserResponseDto $administrator) use ($request, $factory, $userId) {
            $body = $factory->body($request);

            return $this->users->setGoogle($administrator->id, $userId, $factory->requiredString($body, 'google_email'));
        });
    }

    public function removeGoogle(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $userId): ResponseInterface
    {
        return $this->run($request, $response, $access, fn (CurrentUserResponseDto $administrator) => $this->users->removeGoogle($administrator->id, $userId));
    }

    public function roles(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $userId, UserAdministrationRequestFactory $factory): ResponseInterface
    {
        return $this->run($request, $response, $access, function (CurrentUserResponseDto $administrator) use ($request, $factory, $userId) {
            return $this->users->roles(new UpdateUserRolesRequestDto($administrator->id, $userId, $factory->roles($factory->body($request))));
        });
    }

    public function status(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, string $userId, UserAdministrationRequestFactory $factory): ResponseInterface
    {
        return $this->run($request, $response, $access, function (CurrentUserResponseDto $administrator) use ($request, $factory, $userId) {
            return $this->users->status(new UpdateUserStatusRequestDto($administrator->id, $userId, $factory->requiredString($factory->body($request), 'status')));
        });
    }

    private function run(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access, callable $operation, int $status = 200): ResponseInterface
    {
        $administrator = $this->administrator($request, $response, $access);
        if ($administrator instanceof ResponseInterface) {
            return $administrator;
        }

        try {
            return $this->responses->success($response, $operation($administrator), $this->requestId($request), $status);
        } catch (InvalidArgumentException $exception) {
            return $this->responses->problem($response, 'VALIDATION_FAILED', $exception->getMessage(), 422, $this->requestId($request));
        } catch (DomainException $exception) {
            return $this->responses->problem($response, 'STATE_CONFLICT', $exception->getMessage(), 409, $this->requestId($request));
        }
    }

    private function administrator(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $access): CurrentUserResponseDto|ResponseInterface
    {
        try {
            $user = $this->auth->currentUser($access);
            if (in_array('ADMIN', $user->roles, true)) {
                return $user;
            }

            return $this->responses->problem($response, 'FORBIDDEN', 'Permissão insuficiente.', 403, $this->requestId($request));
        } catch (\Throwable) {
            return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais inválidas ou expiradas.', 401, $this->requestId($request));
        }
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
