<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance;

use App\Application\Identity\Service\AuthService;
use App\Application\Performance\Service\AppendAnswerService;
use App\Application\Performance\Service\BasicStatisticsService;
use App\Application\Performance\Service\GetBasicStatisticsService;
use App\Application\Performance\Service\StartAttemptService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Performance\DoctrineAttemptRepository;
use App\Infrastructure\Persistence\Doctrine\Performance\DoctrinePerformanceStatisticsRepository;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineNotebookRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\Performance\Controller\AttemptController;
use App\Interface\Http\Performance\Controller\StatisticsController;
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
        $transactions = new DoctrineTransactionManager($entityManager);
        $attempts = new DoctrineAttemptRepository($entityManager);
        $attemptController = new AttemptController(
            $this->authentication,
            new StartAttemptService(new DoctrineNotebookRepository($entityManager), $attempts, $transactions),
            new AppendAnswerService($attempts, $transactions),
            $this->responses,
        );
        $statisticsController = new StatisticsController(
            $this->authentication,
            new GetBasicStatisticsService(
                new DoctrinePerformanceStatisticsRepository($entityManager),
                new BasicStatisticsService(),
            ),
            $this->responses,
        );
        $identity = new IdentityRequestFactory();
        $answers = new AnswerRequestFactory();
        $responses = $this->responses;

        $app->get('/v1/statistics/me', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
        ) use ($statisticsController, $identity, $responses): ResponseInterface {
            try {
                return $statisticsController->mine($request, $response, $identity->accessToken($request));
            } catch (InvalidArgumentException $exception) {
                return self::unauthenticated($responses, $request, $response, $exception);
            }
        });

        $app->post('/v1/notebooks/{notebookId}/questions/{questionId}/attempts', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            array $arguments,
        ) use ($attemptController, $identity, $responses): ResponseInterface {
            try {
                return $attemptController->start($request, $response, $identity->accessToken($request), (string) ($arguments['notebookId'] ?? ''), (string) ($arguments['questionId'] ?? ''));
            } catch (InvalidArgumentException $exception) {
                return self::unauthenticated($responses, $request, $response, $exception);
            }
        });

        $app->post('/v1/attempts/{attemptId}/answers', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            array $arguments,
        ) use ($attemptController, $identity, $answers, $responses): ResponseInterface {
            try {
                $input = $answers->append($request);
                return $attemptController->appendAnswer(
                    $request,
                    $response,
                    $identity->accessToken($request),
                    (string) ($arguments['attemptId'] ?? ''),
                    $input['optionId'],
                    $input['elapsedSeconds'],
                );
            } catch (InvalidArgumentException $exception) {
                return self::validation($responses, $request, $response, $exception);
            }
        });
    }

    private static function unauthenticated(ApiResponseFactory $responses, ServerRequestInterface $request, ResponseInterface $response, InvalidArgumentException $exception): ResponseInterface
    {
        return $responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id'), [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $exception->getMessage()]]);
    }

    private static function validation(ApiResponseFactory $responses, ServerRequestInterface $request, ResponseInterface $response, InvalidArgumentException $exception): ResponseInterface
    {
        if (str_contains($exception->getMessage(), 'Token de acesso')) {
            return self::unauthenticated($responses, $request, $response, $exception);
        }
        return $responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id'), [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $exception->getMessage()]]);
    }
}
