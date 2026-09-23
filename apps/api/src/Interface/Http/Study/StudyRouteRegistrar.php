<?php
declare(strict_types=1);

namespace App\Interface\Http\Study;

use App\Application\Identity\Service\AuthService;
use App\Application\Study\DTO\Request\GetNotebookRequestDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Service\CreateNotebookService;
use App\Application\Study\Service\GetNotebookService;
use App\Application\Study\Service\ListNotebooksService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineNotebookRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrinePublishedQuestionRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\Study\Controller\StudyController;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class StudyRouteRegistrar
{
    public function __construct(
        private readonly ApiResponseFactory $responses,
        private readonly AuthService $authentication,
    ) {
    }

    public function register(App $app): void
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $repository = new DoctrineNotebookRepository($entityManager);
        $mapper = new NotebookResponseMapper();
        $controller = new StudyController(
            $this->authentication,
            new CreateNotebookService($repository, new DoctrinePublishedQuestionRepository($entityManager), $mapper, new DoctrineTransactionManager($entityManager)),
            new GetNotebookService($repository, $mapper),
            new ListNotebooksService($repository, $mapper),
            $this->responses,
        );
        $requests = new StudyRequestFactory();
        $identity = new IdentityRequestFactory();
        $responses = $this->responses;

        $app->get('/v1/notebooks/{id}', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            array $arguments,
        ) use ($controller, $identity, $responses): ResponseInterface {
            try {
                return $controller->getNotebook(
                    $request,
                    $response,
                    $identity->accessToken($request),
                    new GetNotebookRequestDto((string) ($arguments['id'] ?? '')),
                );
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });

        $app->get('/v1/notebooks', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
        ) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try {
                return $controller->listNotebooks($request, $response, $identity->accessToken($request), $requests->listNotebooks($request));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });

        $app->post('/v1/notebooks', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
        ) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try {
                return $controller->createNotebook(
                    $request,
                    $response,
                    $identity->accessToken($request),
                    $requests->createNotebook($request),
                );
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
    }

    private static function invalidRequest(
        ApiResponseFactory $responses,
        ServerRequestInterface $request,
        ResponseInterface $response,
        InvalidArgumentException $exception,
    ): ResponseInterface {
        $missingToken = str_contains($exception->getMessage(), 'Token de acesso');

        return $responses->problem(
            $response,
            $missingToken ? 'UNAUTHENTICATED' : 'VALIDATION_FAILED',
            $missingToken ? 'Credenciais invalidas ou expiradas.' : 'Um ou mais campos sao invalidos.',
            $missingToken ? 401 : 422,
            (string) $request->getAttribute('request_id'),
            [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $exception->getMessage()]],
        );
    }
}
