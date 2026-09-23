<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance;

use App\Application\Identity\Service\AuthService;
use App\Application\Performance\Service\StartAttemptService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Performance\DoctrineAttemptRepository;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineNotebookRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\Performance\Controller\AttemptController;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class PerformanceRouteRegistrar
{
    public function __construct(
        private readonly ApiResponseFactory $responses,
        private readonly AuthService $authentication,
    ) {
    }

    public function register(App $app): void
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $controller = new AttemptController(
            $this->authentication,
            new StartAttemptService(
                new DoctrineNotebookRepository($entityManager),
                new DoctrineAttemptRepository($entityManager),
                new DoctrineTransactionManager($entityManager),
            ),
            $this->responses,
        );
        $identity = new IdentityRequestFactory();
        $responses = $this->responses;

        $app->post('/v1/notebooks/{notebookId}/questions/{questionId}/attempts', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            array $arguments,
        ) use ($controller, $identity, $responses): ResponseInterface {
            try {
                return $controller->start(
                    $request,
                    $response,
                    $identity->accessToken($request),
                    (string) ($arguments['notebookId'] ?? ''),
                    (string) ($arguments['questionId'] ?? ''),
                );
            } catch (InvalidArgumentException $exception) {
                return $responses->problem(
                    $response,
                    'UNAUTHENTICATED',
                    'Credenciais invalidas ou expiradas.',
                    401,
                    (string) $request->getAttribute('request_id'),
                    [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $exception->getMessage()]],
                );
            }
        });
    }
}
