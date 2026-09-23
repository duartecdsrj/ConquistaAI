<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionBank;

use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;
use App\Application\QuestionBank\Service\ListPublishedQuestionsService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrinePublishedQuestionRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\QuestionBank\Controller\PublishedQuestionController;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class PublishedQuestionRouteRegistrar
{
    public function __construct(
        private readonly ApiResponseFactory $responses,
        private readonly AuthService $authentication,
    ) {
    }

    public function register(App $app): void
    {
        $controller = new PublishedQuestionController(
            $this->authentication,
            new ListPublishedQuestionsService(
                new DoctrinePublishedQuestionRepository(DoctrineEntityManagerFactory::create()),
                new PublishedQuestionResponseMapper(),
            ),
            $this->responses,
        );
        $requests = new PublishedQuestionRequestFactory();
        $identity = new IdentityRequestFactory();
        $responses = $this->responses;

        $app->get('/v1/questions', static function (
            ServerRequestInterface $request,
            ResponseInterface $response,
        ) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try {
                return $controller->list(
                    $request,
                    $response,
                    $identity->accessToken($request),
                    $requests->list($request),
                );
            } catch (InvalidArgumentException $exception) {
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
        });
    }
}
