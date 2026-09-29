<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionLearning;

use App\Application\Identity\Service\AuthService;
use App\Application\QuestionLearning\Mapper\QuestionExplanationResponseMapper;
use App\Application\QuestionLearning\Service\GetQuestionExplanationService;
use App\Application\QuestionLearning\Service\RequestQuestionExplanationService;
use App\Domain\QuestionLearning\Service\QuestionExplanationSafetyPolicy;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\Performance\DoctrineAttemptRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrinePublishedQuestionRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionLearning\DoctrineQuestionExplanationExecutionRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\QuestionLearning\Controller\QuestionExplanationController;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class QuestionExplanationRouteRegistrar
{
    public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $authentication) {}

    public function register(App $app): void
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $executions = new DoctrineQuestionExplanationExecutionRepository($entityManager);
        $mapper = new QuestionExplanationResponseMapper();
        $controller = new QuestionExplanationController(
            $this->authentication,
            new RequestQuestionExplanationService(new DoctrinePublishedQuestionRepository($entityManager), new DoctrineAttemptRepository($entityManager), $executions, new QuestionExplanationSafetyPolicy(), $mapper),
            new GetQuestionExplanationService($executions, $mapper),
            $this->responses,
        );
        $requests = new QuestionLearningRequestFactory();
        $identity = new IdentityRequestFactory();
        $safe = function (ServerRequestInterface $request, ResponseInterface $response, callable $operation): ResponseInterface {
            try {
                return $operation();
            } catch (InvalidArgumentException $error) {
                $authenticationError = str_contains($error->getMessage(), 'Token de acesso');
                return $this->responses->problem($response, $authenticationError ? 'UNAUTHENTICATED' : 'VALIDATION_FAILED', $authenticationError ? 'Credenciais invalidas ou expiradas.' : 'Um ou mais campos são inválidos.', $authenticationError ? 401 : 422, (string) $request->getAttribute('request_id'));
            }
        };
        $app->post('/v1/questions/{id}/explanations', static fn (ServerRequestInterface $request, ResponseInterface $response, array $arguments): ResponseInterface => $safe($request, $response, static fn (): ResponseInterface => $controller->request($request, $response, $identity->accessToken($request), $requests->explanation((string) $arguments['id'], $request))));
        $app->get('/v1/questions/{id}/explanations/latest', static fn (ServerRequestInterface $request, ResponseInterface $response, array $arguments): ResponseInterface => $safe($request, $response, static fn (): ResponseInterface => $controller->latest($request, $response, $identity->accessToken($request), (string) $arguments['id'])));
    }
}
