<?php
declare(strict_types=1);

namespace App\Interface\Http\Study;

use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;
use App\Application\Study\DTO\Request\GetNotebookRequestDto;
use App\Application\Study\DTO\Request\ListNotebookQuestionsRequestDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Service\CreateNotebookService;
use App\Application\Study\Service\FinishNotebookService;
use App\Application\Study\Service\GetNotebookStatisticsService;
use App\Application\Study\Service\PauseNotebookService;
use App\Application\Study\Service\GetNotebookService;
use App\Application\Study\Service\ListNotebookQuestionsService;
use App\Application\Study\Service\ListNotebooksService;
use App\Application\Study\Service\StartNotebookService;
use App\Application\Study\Service\GetStudyGoalService;
use App\Application\Study\Service\UpdateStudyGoalService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrinePublishedQuestionRepository;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineStudyGoalRepository;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineStudyContestSubjectRepository;
use App\Application\Study\Service\ListStudyContestSubjectsService;
use App\Interface\Http\Study\Controller\StudyContestController;
use App\Infrastructure\Persistence\Doctrine\Catalog\DoctrinePositionRepository;use App\Infrastructure\Persistence\Doctrine\Catalog\DoctrinePositionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineNotebookRepository;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineDirectedStudyPlanRepository;
use App\Application\Study\Service\CreateDirectedStudyPlanService;
use App\Application\Study\Service\ListDirectedStudyPlansService;
use App\Application\Study\Mapper\DirectedStudyPlanResponseMapper;
use App\Interface\Http\Study\Controller\DirectedStudyPlanController;
use App\Infrastructure\Persistence\Doctrine\Study\DoctrineNotebookProgressReader;
use App\Interface\Http\Study\Controller\StudyGoalController;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\Study\Controller\StudyController;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class StudyRouteRegistrar
{
    public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $authentication)
    {
    }

    public function register(App $app): void
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $notebooks = new DoctrineNotebookRepository($entityManager);
        $questions = new DoctrinePublishedQuestionRepository($entityManager);
        $mapper = new NotebookResponseMapper();
        $controller = new StudyController(
            $this->authentication,
            new CreateNotebookService($notebooks, $questions, $mapper, new DoctrineTransactionManager($entityManager), new DoctrinePositionRepository($entityManager), new DoctrinePositionTaxonomyAssignmentRepository($entityManager), new DoctrineStudyContestSubjectRepository($entityManager)),
            new GetNotebookService($notebooks, $mapper),
            new ListNotebooksService($notebooks, $mapper),
            new ListNotebookQuestionsService($notebooks, $questions, new PublishedQuestionResponseMapper()),
            new StartNotebookService($notebooks, $mapper, new DoctrineTransactionManager($entityManager)),
            new PauseNotebookService($notebooks, $mapper, new DoctrineTransactionManager($entityManager)),
            new FinishNotebookService($notebooks, $mapper, new DoctrineTransactionManager($entityManager)),
            new GetNotebookStatisticsService($notebooks, new DoctrineNotebookProgressReader($entityManager)),
            $this->responses,
        );
        $contest = new StudyContestController($this->authentication, new ListStudyContestSubjectsService(new DoctrineStudyContestSubjectRepository($entityManager)), $this->responses);
        $directedPlans = new DirectedStudyPlanController($this->authentication, new CreateDirectedStudyPlanService(new DoctrineDirectedStudyPlanRepository($entityManager), new DoctrinePositionRepository($entityManager), new DirectedStudyPlanResponseMapper(), new DoctrineTransactionManager($entityManager)), new ListDirectedStudyPlansService(new DoctrineDirectedStudyPlanRepository($entityManager), new DirectedStudyPlanResponseMapper()), $this->responses);
        $goals = new DoctrineStudyGoalRepository($entityManager);
        $goalReader = new GetStudyGoalService($goals);
        $goalController = new StudyGoalController(
            $this->authentication,
            $goalReader,
            new UpdateStudyGoalService($goals, $goalReader),
            $this->responses,
        );
        $requests = new StudyRequestFactory();
        $identity = new IdentityRequestFactory();
        $responses = $this->responses;

        $app->get('/v1/study-positions/{positionId}/subjects', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($contest, $identity): ResponseInterface { return $contest->subjects($request, $response, $identity->accessToken($request), (string) ($arguments['positionId'] ?? '')); });

        $app->get('/v1/directed-study-plans', static function (ServerRequestInterface $request, ResponseInterface $response) use ($directedPlans, $identity): ResponseInterface { return $directedPlans->list($request, $response, $identity->accessToken($request)); });
        $app->post('/v1/directed-study-plans', static function (ServerRequestInterface $request, ResponseInterface $response) use ($directedPlans, $identity, $responses): ResponseInterface { try { $body=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR); if(!is_array($body)||!is_string($body['name']??null)||!is_string($body['exam_id']??null)||!is_string($body['position_id']??null)) throw new InvalidArgumentException(); return $directedPlans->create($request,$response,$identity->accessToken($request),$body['name'],$body['exam_id'],$body['position_id']); } catch (\JsonException|InvalidArgumentException) { return self::invalidRequest($responses,$request,$response,new InvalidArgumentException('Campos do plano invalidos.')); } });

        $app->get('/v1/study-goals/me', static function (ServerRequestInterface $request, ResponseInterface $response) use ($goalController, $identity, $responses): ResponseInterface {
            try {
                return $goalController->get($request, $response, $identity->accessToken($request));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
        $app->put('/v1/study-goals/me', static function (ServerRequestInterface $request, ResponseInterface $response) use ($goalController, $identity, $requests, $responses): ResponseInterface {
            try {
                return $goalController->update($request, $response, $identity->accessToken($request), $requests->updateGoal($request));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });

        $app->post('/v1/notebooks/{id}/start', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($controller, $identity, $responses): ResponseInterface {
            try {
                return $controller->startNotebook($request, $response, $identity->accessToken($request), new GetNotebookRequestDto((string) ($arguments['id'] ?? '')));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
        $app->post('/v1/notebooks/{id}/pause', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($controller, $identity, $responses): ResponseInterface {
            try { return $controller->pauseNotebook($request, $response, $identity->accessToken($request), new GetNotebookRequestDto((string) ($arguments['id'] ?? ''))); }
            catch (InvalidArgumentException $exception) { return self::invalidRequest($responses, $request, $response, $exception); }
        });
        $app->get('/v1/notebooks/{id}/statistics', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($controller, $identity, $responses): ResponseInterface {
            try { return $controller->statistics($request, $response, $identity->accessToken($request), new GetNotebookRequestDto((string) ($arguments['id'] ?? ''))); }
            catch (InvalidArgumentException $exception) { return self::invalidRequest($responses, $request, $response, $exception); }
        });
        $app->post('/v1/notebooks/{id}/finish', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($controller, $identity, $responses): ResponseInterface {
            try {
                return $controller->finishNotebook($request, $response, $identity->accessToken($request), new GetNotebookRequestDto((string) ($arguments['id'] ?? '')));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
        $app->get('/v1/notebooks/{id}/questions', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try {
                return $controller->listNotebookQuestions($request, $response, $identity->accessToken($request), new ListNotebookQuestionsRequestDto((string) ($arguments['id'] ?? ''), $requests->page($request), $requests->perPage($request)));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
        $app->get('/v1/notebooks/{id}', static function (ServerRequestInterface $request, ResponseInterface $response, array $arguments) use ($controller, $identity, $responses): ResponseInterface {
            try {
                return $controller->getNotebook($request, $response, $identity->accessToken($request), new GetNotebookRequestDto((string) ($arguments['id'] ?? '')));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
        $app->get('/v1/notebooks', static function (ServerRequestInterface $request, ResponseInterface $response) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try {
                return $controller->listNotebooks($request, $response, $identity->accessToken($request), $requests->listNotebooks($request));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
        $app->post('/v1/notebooks', static function (ServerRequestInterface $request, ResponseInterface $response) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try {
                return $controller->createNotebook($request, $response, $identity->accessToken($request), $requests->createNotebook($request));
            } catch (InvalidArgumentException $exception) {
                return self::invalidRequest($responses, $request, $response, $exception);
            }
        });
    }

    private static function invalidRequest(ApiResponseFactory $responses, ServerRequestInterface $request, ResponseInterface $response, InvalidArgumentException $exception): ResponseInterface
    {
        $missingToken = str_contains($exception->getMessage(), 'Token de acesso');

        return $responses->problem($response, $missingToken ? 'UNAUTHENTICATED' : 'VALIDATION_FAILED', $missingToken ? 'Credenciais invalidas ou expiradas.' : 'Um ou mais campos sao invalidos.', $missingToken ? 401 : 422, (string) $request->getAttribute('request_id'), [['field' => 'request', 'code' => 'INVALID_VALUE', 'message' => $exception->getMessage()]]);
    }
}
