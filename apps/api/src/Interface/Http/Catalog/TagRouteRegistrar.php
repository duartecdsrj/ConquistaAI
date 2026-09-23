<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog;

use App\Application\Catalog\DTO\Request\CreateTagRequestDto;
use App\Application\Catalog\Mapper\TagResponseMapper;
use App\Application\Catalog\Service\CreateTagService;
use App\Application\Catalog\Service\ListTagsService;
use App\Application\Identity\Service\AuthService;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\Catalog\DoctrineTagRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Interface\Http\Identity\IdentityRequestFactory;
use DomainException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class TagRouteRegistrar
{
    public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $auth) {}

    public function register(App $app): void
    {
        $em = DoctrineEntityManagerFactory::create();
        $repo = new DoctrineTagRepository($em);
        $create = new CreateTagService($repo, new TagResponseMapper(), new DoctrineTransactionManager($em));
        $list = new ListTagsService($repo, new TagResponseMapper());
        $identity = new IdentityRequestFactory();

        $app->get('/v1/tags', function (ServerRequestInterface $request, ResponseInterface $response) use ($identity, $list): ResponseInterface {
            $user = $this->user($request, $response, $identity);
            return $user instanceof ResponseInterface ? $user : $this->responses->success($response, $list->list(), (string) $request->getAttribute('request_id'));
        });
        $app->post('/v1/tags', function (ServerRequestInterface $request, ResponseInterface $response) use ($identity, $create): ResponseInterface {
            $user = $this->admin($request, $response, $identity);
            if ($user instanceof ResponseInterface) return $user;
            try {
                $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($payload) || !is_string($payload['name'] ?? null)) throw new InvalidArgumentException('Informe name.');
                return $this->responses->success($response, $create->create(new CreateTagRequestDto($payload['name'])), (string) $request->getAttribute('request_id'), 201);
            } catch (InvalidArgumentException|DomainException $exception) {
                return $this->responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id'));
            }
        });
    }

    private function user(ServerRequestInterface $request, ResponseInterface $response, IdentityRequestFactory $identity): CurrentUserResponseDto|ResponseInterface
    {
        try { return $this->auth->currentUser($identity->accessToken($request)); }
        catch (DomainException|UnavailableUserException|InvalidArgumentException) { return $this->responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
    }

    private function admin(ServerRequestInterface $request, ResponseInterface $response, IdentityRequestFactory $identity): CurrentUserResponseDto|ResponseInterface
    {
        $user = $this->user($request, $response, $identity);
        return $user instanceof ResponseInterface || in_array('ADMIN', $user->roles, true) ? $user : $this->responses->problem($response, 'FORBIDDEN', 'Permissao insuficiente.', 403, (string) $request->getAttribute('request_id'));
    }
}
