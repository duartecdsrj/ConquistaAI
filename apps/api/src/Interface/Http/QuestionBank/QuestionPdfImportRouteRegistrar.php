<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank;
use App\Application\QuestionBank\Mapper\QuestionPdfImportJobResponseMapper;
use App\Application\QuestionBank\Service\GetQuestionPdfImportJobService;use App\Application\QuestionBank\Service\CancelQuestionPdfImportJobService;
use App\Application\QuestionBank\Service\ListQuestionPdfImportJobsService;
use App\Application\QuestionBank\Service\QueueQuestionPdfImportService;use App\Application\QuestionBank\Service\AskQuestionWithPdfEvidenceService;
use App\Application\Identity\Service\AuthService;
use App\Infrastructure\Database;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfImportJobRepository;use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionAssetRecord;use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Storage\LocalQuestionPdfDocumentStorage;use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfEvidenceSourceRepository;use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;use App\Infrastructure\Assistant\ConfiguredAssistantProviderFactory;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\QuestionBank\Controller\QuestionPdfImportController;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
final class QuestionPdfImportRouteRegistrar
{
    public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $auth) {}
    public function register(App $app): void
    {
        $em = DoctrineEntityManagerFactory::create(); $mapper = new QuestionPdfImportJobResponseMapper(); $jobs = new DoctrineQuestionPdfImportJobRepository($em);
        $controller = new QuestionPdfImportController($this->auth, new QueueQuestionPdfImportService($jobs, new LocalQuestionPdfDocumentStorage(Database::env('QUESTION_PDF_DOCUMENT_DIRECTORY', '/app/storage/question-pdfs')), $mapper, new DoctrineTransactionManager($em)), new GetQuestionPdfImportJobService($jobs, $mapper), new ListQuestionPdfImportJobsService($jobs, $mapper), new CancelQuestionPdfImportJobService($jobs), $this->responses);
        $questionHelp = new AskQuestionWithPdfEvidenceService(new DoctrineQuestionPdfEvidenceSourceRepository($em), new PdftotextPdfTextExtractor(), ConfiguredAssistantProviderFactory::create());
        $input = new QuestionPdfImportRequestFactory(); $identity = new IdentityRequestFactory(); $responses = $this->responses; $auth = $this->auth;
        $app->post('/v1/questions/{id}/pdf-assistance', static function(ServerRequestInterface $request,ResponseInterface $response,array $args)use($questionHelp,$identity,$responses,$auth):ResponseInterface { try { $auth->currentUser($identity->accessToken($request)); $body=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR); if(!is_array($body)||!is_string($body['question']??null)) throw new InvalidArgumentException(); return $responses->success($response,$questionHelp->ask((string)($args['id']??''),$body['question']),(string)$request->getAttribute('request_id')); } catch (\DomainException|\RuntimeException) { return $responses->problem($response,'VALIDATION_FAILED','Não foi possível consultar a fonte PDF desta questão.',422,(string)$request->getAttribute('request_id')); } catch (\JsonException|InvalidArgumentException) { return $responses->problem($response,'VALIDATION_FAILED','Informe uma dúvida válida.',422,(string)$request->getAttribute('request_id')); } });
                $app->get('/v1/question-assets/{id}',static function(ServerRequestInterface $request,ResponseInterface $response,array $args)use($em,$identity,$responses,$auth):ResponseInterface{try{$user=$auth->currentUser($identity->accessToken($request));}catch(\DomainException|UnavailableUserException|\InvalidArgumentException){return $responses->problem($response,'UNAUTHENTICATED','Credenciais invalidas ou expiradas.',401,(string)$request->getAttribute('request_id'));}$asset=$em->find(QuestionAssetRecord::class,(string)$args['id']);if(!$asset instanceof QuestionAssetRecord||!str_starts_with($asset->path,'/app/storage/question-pdf-assets/')||!is_file($asset->path))return $responses->problem($response,'RESOURCE_NOT_FOUND','Recurso nao encontrado.',404,(string)$request->getAttribute('request_id'));$question=$em->find(QuestionRecord::class,$asset->questionId);if(!$question instanceof QuestionRecord||($question->status!=='PUBLISHED'&&!in_array('ADMIN',$user->roles,true)))return $responses->problem($response,'FORBIDDEN','Permissao insuficiente.',403,(string)$request->getAttribute('request_id'));$response->getBody()->write((string)file_get_contents($asset->path));return $response->withHeader('Content-Type',$asset->mimeType)->withHeader('Cache-Control','private, max-age=3600');});
        $app->post('/v1/admin/question-pdf-imports', static function (ServerRequestInterface $request, ResponseInterface $response) use ($controller, $input, $identity, $responses): ResponseInterface { try { return $controller->queue($request, $response, $identity->accessToken($request), $input->queue($request)); } catch (InvalidArgumentException) { return $responses->problem($response, 'VALIDATION_FAILED', 'Um ou mais campos sao invalidos.', 422, (string) $request->getAttribute('request_id')); } });
        $app->get('/v1/admin/question-pdf-imports', static function (ServerRequestInterface $request, ResponseInterface $response) use ($controller, $input, $identity, $responses): ResponseInterface { try { return $controller->list($request, $response, $identity->accessToken($request), $input->list($request)); } catch (InvalidArgumentException) { return $responses->problem($response, 'VALIDATION_FAILED', 'Parametros de paginacao invalidos.', 422, (string) $request->getAttribute('request_id')); } });
        $app->post('/v1/admin/question-pdf-imports/{id}/cancel', static fn (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface => $controller->cancel($request, $response, $identity->accessToken($request), (string) $args['id']));
        $app->get('/v1/admin/question-pdf-imports/{id}', static fn (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface => $controller->get($request, $response, $identity->accessToken($request), (string) $args['id']));
    }
}
