<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Identity\Mapper\IdentityResponseMapper;
use App\Application\Catalog\Mapper\ExamResponseMapper;
use App\Application\Catalog\Service\CreateExamService;
use App\Application\Catalog\Service\ListExamsService;
use App\Application\Catalog\Service\GetExamService;
use App\Application\Catalog\Service\UpdateExamService;
use App\Application\Catalog\Service\DeleteExamService;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\Service\PreviewQuestionImportService;
use App\Application\QuestionBank\Service\GetQuestionImportService;
use App\Application\QuestionBank\Service\CommitQuestionImportService;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineImportedQuestionWriter;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Http\CorsMiddleware;
use App\Infrastructure\Http\RequestIdMiddleware;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineAuthEventRepository;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineAuthSessionRepository;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineUserRepository;
use App\Infrastructure\Persistence\Doctrine\Catalog\DoctrineExamRepository;
use App\Infrastructure\Security\HmacJwtAccessTokenService;
use App\Infrastructure\Security\NativePasswordHasher;
use App\Infrastructure\Security\RandomRefreshTokenGenerator;
use App\Infrastructure\Security\Sha256IpAddressHasher;
use App\Infrastructure\Security\SystemClock;
use App\Infrastructure\Import\JsonCsvQuestionImportReader;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionImportRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionDuplicateDetector;
use App\Interface\Http\Identity\Controller\AuthController;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\Catalog\CatalogRequestFactory;
use App\Interface\Http\Catalog\Controller\CatalogController;
use App\Interface\Http\Catalog\CatalogRouteRegistrar;
use App\Interface\Http\Catalog\TagRouteRegistrar;
use App\Interface\Http\Catalog\SubjectRouteRegistrar;
use App\Interface\Http\QuestionBank\Controller\QuestionImportController;
use App\Interface\Http\QuestionBank\QuestionImportRequestFactory;
use App\Interface\Http\Study\StudyRouteRegistrar;
use App\Interface\Http\QuestionBank\PublishedQuestionRouteRegistrar;
use App\Interface\Http\QuestionBank\EditorialQuestionRouteRegistrar;
use App\Interface\Http\Performance\PerformanceRouteRegistrar;
use App\Interface\Http\Taxonomy\TaxonomyRouteRegistrar;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory as SlimAppFactory;

final class AppFactory
{
    public static function create(): App
    {
        $app = SlimAppFactory::create();
        $responses = new ApiResponseFactory();
        $identityRequests = new IdentityRequestFactory();
        $auth = self::authController($responses);
        $catalogRequests = new CatalogRequestFactory();
        $catalog = self::catalogController($responses, self::authService());
        (new CatalogRouteRegistrar($responses, self::authService()))->register($app);
        (new TagRouteRegistrar($responses, self::authService()))->register($app);
        (new SubjectRouteRegistrar($responses, self::authService()))->register($app);
        $importRequests = new QuestionImportRequestFactory();
        (new StudyRouteRegistrar($responses, self::authService()))->register($app);
        $imports = self::questionImportController($responses, self::authService());
        (new PerformanceRouteRegistrar($responses, self::authService()))->register($app);
        (new PublishedQuestionRouteRegistrar($responses, self::authService()))->register($app);
        (new EditorialQuestionRouteRegistrar($responses, self::authService()))->register($app);
        (new TaxonomyRouteRegistrar($responses, self::authService()))->register($app);

        $app->get('/health', static function (ServerRequestInterface $request, ResponseInterface $response) use ($responses): ResponseInterface {
            return $responses->success($response, ['status' => 'ok'], (string) $request->getAttribute('request_id'));
        });

        $app->post('/v1/auth/login', static function (ServerRequestInterface $request, ResponseInterface $response) use ($auth, $identityRequests, $responses): ResponseInterface {
            try {
                return $auth->login($request, $response, $identityRequests->login($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->post('/v1/auth/refresh', static function (ServerRequestInterface $request, ResponseInterface $response) use ($auth, $identityRequests, $responses): ResponseInterface {
            try {
                return $auth->refresh($request, $response, $identityRequests->refresh($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->post('/v1/auth/logout', static function (ServerRequestInterface $request, ResponseInterface $response) use ($auth, $identityRequests, $responses): ResponseInterface {
            try {
                return $auth->logout($request, $response, $identityRequests->logout($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->get('/v1/auth/me', static function (ServerRequestInterface $request, ResponseInterface $response) use ($auth, $identityRequests, $responses): ResponseInterface {
            try {
                return $auth->me($request, $response, $identityRequests->accessToken($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });

        $app->get('/v1/exams', static function (ServerRequestInterface $request, ResponseInterface $response) use ($catalog, $identityRequests, $responses): ResponseInterface {
            try {
                return $catalog->listExams($request, $response, $identityRequests->accessToken($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->post('/v1/exams', static function (ServerRequestInterface $request, ResponseInterface $response) use ($catalog, $catalogRequests, $identityRequests, $responses): ResponseInterface {
            try {
                return $catalog->createExam($request, $response, $identityRequests->accessToken($request), $catalogRequests->createExam($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });

        $app->get('/v1/exams/{id}', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($catalog, $identityRequests, $responses): ResponseInterface {
            try {
                return $catalog->getExam($request, $response, $identityRequests->accessToken($request), (string) ($arguments['id'] ?? ''));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->put('/v1/exams/{id}', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($catalog, $catalogRequests, $identityRequests, $responses): ResponseInterface {
            try {
                return $catalog->updateExam($request, $response, $identityRequests->accessToken($request), $catalogRequests->updateExam($request, (string) ($arguments['id'] ?? '')));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->delete('/v1/exams/{id}', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($catalog, $identityRequests, $responses): ResponseInterface {
            try {
                return $catalog->deleteExam($request, $response, $identityRequests->accessToken($request), (string) ($arguments['id'] ?? ''));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });

        $app->post('/v1/question-imports', static function (ServerRequestInterface $request, ResponseInterface $response) use ($imports, $importRequests, $identityRequests, $responses): ResponseInterface {
            try {
                return $imports->preview($request, $response, $identityRequests->accessToken($request), $importRequests->preview($request));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });

        $app->add(new RequestIdMiddleware());
        $app->get('/v1/question-imports/{id}', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($imports, $identityRequests, $responses): ResponseInterface {
            try {
                return $imports->get($request, $response, $identityRequests->accessToken($request), (string) ($arguments['id'] ?? ''));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });
        $app->post('/v1/question-imports/{id}/commit', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($imports, $importRequests, $identityRequests, $responses): ResponseInterface {
            try {
                $access = $identityRequests->accessToken($request);
                return $imports->commit($request, $response, $access, $importRequests->commit($request, '', (string) ($arguments['id'] ?? '')));
            } catch (InvalidArgumentException $exception) {
                return self::validationProblem($responses, $request, $response, $exception->getMessage());
            }
        });


        $app->add(new CorsMiddleware());
$errorMiddleware = $app->addErrorMiddleware(false, true, true);
        $errorMiddleware->setDefaultErrorHandler(
            static function (ServerRequestInterface $request, \Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($responses): ResponseInterface {
                $notFound = $exception instanceof \Slim\Exception\HttpNotFoundException;
                return $responses->problem(
                    new \Slim\Psr7\Response(),
                    $notFound ? 'RESOURCE_NOT_FOUND' : 'INTERNAL_ERROR',
                    $notFound ? 'Recurso nao encontrado.' : 'Ocorreu um erro interno.',
                    $notFound ? 404 : 500,
                    (string) $request->getAttribute('request_id'),
                );
            },
        );
        return $app;
    }

    private static function authService(): AuthService
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $mapper = new IdentityResponseMapper();
        $tokens = new HmacJwtAccessTokenService();

        return new AuthService(
            new DoctrineUserRepository($entityManager),
            new DoctrineAuthSessionRepository($entityManager),
            new DoctrineAuthEventRepository($entityManager),
            new NativePasswordHasher(),
            $tokens,
            $tokens,
            new RandomRefreshTokenGenerator(),
            new Sha256IpAddressHasher(),
            new SystemClock(),
            new DoctrineTransactionManager($entityManager),
            $mapper,
            (int) Database::env('REFRESH_TOKEN_TTL', '2592000'),
        );
    }


    private static function authController(ApiResponseFactory $responses): AuthController
    {
        return new AuthController(
            self::authService(),
            new IdentityResponseMapper(),
            $responses,
        );
    }

    private static function questionImportController(ApiResponseFactory $responses, AuthService $authentication): QuestionImportController
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $repository = new DoctrineQuestionImportRepository($entityManager);

        return new QuestionImportController(
            $authentication,
            new PreviewQuestionImportService(
                new JsonCsvQuestionImportReader(),
                new \App\Application\QuestionBank\Service\QuestionImportValidationService(),
                $repository,
                new DoctrineTransactionManager($entityManager),
                new DoctrineQuestionDuplicateDetector($entityManager),
            ),
            new GetQuestionImportService($repository),
            new CommitQuestionImportService($repository, new DoctrineImportedQuestionWriter($entityManager), new DoctrineTransactionManager($entityManager)),
            $responses,
        );
    }



    private static function catalogController(ApiResponseFactory $responses, AuthService $authentication): CatalogController
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $mapper = new ExamResponseMapper();

        return new CatalogController(
            $authentication,
            new CreateExamService(new DoctrineExamRepository($entityManager), $mapper, new DoctrineTransactionManager($entityManager)),
            new ListExamsService(new DoctrineExamRepository($entityManager), $mapper),
            new GetExamService(new DoctrineExamRepository($entityManager), $mapper),
            new UpdateExamService(new DoctrineExamRepository($entityManager), $mapper, new DoctrineTransactionManager($entityManager)),
            new DeleteExamService(new DoctrineExamRepository($entityManager), new DoctrineTransactionManager($entityManager)),
            $responses,
        );
    }

    private static function validationProblem(ApiResponseFactory $responses, ServerRequestInterface $request, ResponseInterface $response, string $message): ResponseInterface
    {
        return $responses->problem(
            $response,
            'VALIDATION_FAILED',
            'Um ou mais campos sao invalidos.',
            422,
            (string) $request->getAttribute('request_id'),
            [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $message]],
        );
    }
}
