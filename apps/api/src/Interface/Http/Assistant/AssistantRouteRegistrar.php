<?php
declare(strict_types=1);
namespace App\Interface\Http\Assistant;
use App\Application\Assistant\Mapper\AssistantResponseMapper;
use App\Application\Assistant\Service\CreateAssistantConversationService;
use App\Application\Assistant\Service\ListAssistantConversationsService;
use App\Application\Assistant\Service\ListAssistantMessagesService;
use App\Application\Assistant\Service\ListAvailableAssistantSyllabiService;
use App\Application\Assistant\Service\SendAssistantMessageService;
use App\Application\Identity\Service\AuthService;
use App\Infrastructure\Assistant\DeterministicSyllabusAssistantProvider;
use App\Infrastructure\Assistant\DoctrineSyllabusEvidenceRetriever;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Persistence\Doctrine\Assistant\DoctrineAssistantConversationRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Interface\Http\Assistant\Controller\AssistantController;
use App\Interface\Http\Identity\IdentityRequestFactory;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
final class AssistantRouteRegistrar { public function __construct(private readonly ApiResponseFactory $responses, private readonly AuthService $authentication) {} public function register(App $app): void { $em=DoctrineEntityManagerFactory::create(); $repository=new DoctrineAssistantConversationRepository($em); $evidence=new DoctrineSyllabusEvidenceRetriever($em); $mapper=new AssistantResponseMapper(); $controller=new AssistantController($this->authentication,new CreateAssistantConversationService($repository,$evidence,$mapper),new ListAssistantConversationsService($repository,$mapper),new ListAssistantMessagesService($repository,$mapper),new SendAssistantMessageService($repository,$evidence,new DeterministicSyllabusAssistantProvider(),$mapper),new ListAvailableAssistantSyllabiService($evidence),$this->responses); $requests=new AssistantRequestFactory(); $identity=new IdentityRequestFactory(); $responses=$this->responses;
$app->get('/v1/assistant/syllabi',static function(ServerRequestInterface $r,ResponseInterface $s)use($controller,$identity,$responses):ResponseInterface { try{return $controller->availableSyllabi($r,$s,$identity->accessToken($r));}catch(InvalidArgumentException $e){return self::invalid($responses,$r,$s,$e);}});
$app->get('/v1/assistant/conversations',static function(ServerRequestInterface $r,ResponseInterface $s)use($controller,$identity,$responses):ResponseInterface { try{return $controller->list($r,$s,$identity->accessToken($r));}catch(InvalidArgumentException $e){return self::invalid($responses,$r,$s,$e);}});
$app->post('/v1/assistant/conversations',static function(ServerRequestInterface $r,ResponseInterface $s)use($controller,$identity,$requests,$responses):ResponseInterface { try{return $controller->create($r,$s,$identity->accessToken($r),$requests->conversation($r));}catch(InvalidArgumentException $e){return self::invalid($responses,$r,$s,$e);}});
$app->get('/v1/assistant/conversations/{id}/messages',static function(ServerRequestInterface $r,ResponseInterface $s,array $a)use($controller,$identity,$responses):ResponseInterface { try{return $controller->messages($r,$s,$identity->accessToken($r),(string)($a['id']??''));}catch(InvalidArgumentException $e){return self::invalid($responses,$r,$s,$e);}});
$app->post('/v1/assistant/conversations/{id}/messages',static function(ServerRequestInterface $r,ResponseInterface $s,array $a)use($controller,$identity,$requests,$responses):ResponseInterface { try{return $controller->send($r,$s,$identity->accessToken($r),$requests->message($r,(string)($a['id']??'')));}catch(InvalidArgumentException $e){return self::invalid($responses,$r,$s,$e);}}); }
private static function invalid(ApiResponseFactory $responses,ServerRequestInterface $r,ResponseInterface $s,InvalidArgumentException $e):ResponseInterface{return $responses->problem($s,'MALFORMED_REQUEST',$e->getMessage(),400,(string)$r->getAttribute('request_id'));} }
