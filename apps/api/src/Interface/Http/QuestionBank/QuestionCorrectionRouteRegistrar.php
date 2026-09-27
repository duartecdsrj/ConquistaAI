<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank;
use App\Application\Identity\Service\AuthService;
use App\Application\QuestionBank\Mapper\QuestionCorrectionRequestResponseMapper;
use App\Application\QuestionBank\Service\QuestionCorrectionRequestService;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionCorrectionRequestRepository;
use App\Interface\Http\Identity\IdentityRequestFactory;
use App\Interface\Http\QuestionBank\Controller\QuestionCorrectionRequestController;
use Psr\Http\Message\ResponseInterface;use Psr\Http\Message\ServerRequestInterface;use Slim\App;
final class QuestionCorrectionRouteRegistrar { public function __construct(private readonly ApiResponseFactory $responses,private readonly AuthService $auth){} public function register(App $app):void{$controller=new QuestionCorrectionRequestController($this->auth,new QuestionCorrectionRequestService(new DoctrineQuestionCorrectionRequestRepository(DoctrineEntityManagerFactory::create()),new QuestionCorrectionRequestResponseMapper()),$this->responses);$identity=new IdentityRequestFactory();$factory=new QuestionCorrectionRequestFactory();$app->post('/v1/questions/{id}/correction-requests',static fn(ServerRequestInterface $r,ResponseInterface $p,array $a):ResponseInterface=>$controller->create($r,$p,$identity->accessToken($r),(string)($a['id']??''),fn(string $userId)=>$factory->create($r,(string)($a['id']??''),$userId)));$app->get('/v1/questions/{id}/correction-requests/latest',static fn(ServerRequestInterface $r,ResponseInterface $p,array $a):ResponseInterface=>$controller->latest($r,$p,$identity->accessToken($r),(string)($a['id']??'')));$app->post('/v1/admin/question-correction-requests/{id}/approve',static fn(ServerRequestInterface $r,ResponseInterface $p,array $a):ResponseInterface=>$controller->approve($r,$p,$identity->accessToken($r),(string)($a['id']??'')));}}
