<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog;

use App\Application\Catalog\Mapper\SubjectResponseMapper;
use App\Application\Catalog\Service\CreateSubjectService;
use App\Application\Catalog\Service\ListSubjectsService;
use App\Application\Identity\Service\AuthService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\Catalog\DoctrineSubjectRepository;
use App\Infrastructure\Persistence\Doctrine\Catalog\DoctrineSyllabusRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Interface\Http\Catalog\Controller\CreateSubjectController;
use App\Interface\Http\Identity\IdentityRequestFactory;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

final class SubjectRouteRegistrar
{
    public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $auth) {}
    public function register(App $app): void
    {
        $em = DoctrineEntityManagerFactory::create();
        $repository = new DoctrineSubjectRepository($em);
        $mapper = new SubjectResponseMapper();
        $list = new ListSubjectsService($repository, $mapper);
        $create = new CreateSubjectController($this->auth, new CreateSubjectService($repository, new DoctrineSyllabusRepository($em), $mapper, new DoctrineTransactionManager($em)), $this->responses);
        $identity = new IdentityRequestFactory();
        $factory = new SubjectRequestFactory();
        $responses = $this->responses;
        $auth = $this->auth;

        $app->get('/v1/syllabi/{syllabusId}/subjects', static function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($identity, $list, $responses, $auth): ResponseInterface {
            try { $auth->currentUser($identity->accessToken($request)); return $responses->success($response, $list->list((string) $args['syllabusId']), (string) $request->getAttribute('request_id')); }
            catch (\DomainException|\App\Domain\Identity\Exception\UnavailableUserException|InvalidArgumentException) { return $responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
        });
        $app->post('/v1/subjects', static function (ServerRequestInterface $request, ResponseInterface $response) use ($identity, $factory, $create, $responses): ResponseInterface {
            try { return $create->create($request, $response, $identity->accessToken($request), $factory->create($request)); }
            catch (InvalidArgumentException) { return $responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id')); }
        });
    }
}
