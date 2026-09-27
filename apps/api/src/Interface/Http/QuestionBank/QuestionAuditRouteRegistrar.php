<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\Mapper\QuestionAuditReportResponseMapper;
use App\Application\QuestionBank\Mapper\QuestionAuditFindingResponseMapper;
use App\Application\QuestionBank\Service\GetLatestQuestionAuditReportService;
use App\Application\QuestionBank\Service\ListLatestQuestionAuditFindingsService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionAuditRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\QuestionBank\Controller\QuestionAuditController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
final class QuestionAuditRouteRegistrar {
    public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $auth) {}
    public function register(App $app): void {
        $reports = new DoctrineQuestionAuditRepository(DoctrineEntityManagerFactory::create());
        $controller = new QuestionAuditController($this->auth, new GetLatestQuestionAuditReportService($reports, new QuestionAuditReportResponseMapper()), new ListLatestQuestionAuditFindingsService($reports, new QuestionAuditFindingResponseMapper()), $this->responses);
        $requests = new QuestionAuditRequestFactory();
        $identity = new IdentityRequestFactory(); $responses = $this->responses;
        $app->get('/v1/admin/question-audits/latest', static function (ServerRequestInterface $request, ResponseInterface $response) use ($controller, $identity, $responses): ResponseInterface {
            try { return $controller->latest($request, $response, $identity->accessToken($request)); }
            catch (\InvalidArgumentException) { return $responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
        });
        $app->get('/v1/admin/question-audits/latest/findings', static function (ServerRequestInterface $request, ResponseInterface $response) use ($controller, $requests, $identity, $responses): ResponseInterface {
            try { $access = $identity->accessToken($request); }
            catch (\InvalidArgumentException) { return $responses->problem($response, 'UNAUTHENTICATED', 'Credenciais invalidas ou expiradas.', 401, (string) $request->getAttribute('request_id')); }
            try { return $controller->findings($request, $response, $access, $requests->findings($request)); }
            catch (\InvalidArgumentException) { return $responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id')); }
        });
    }
}
