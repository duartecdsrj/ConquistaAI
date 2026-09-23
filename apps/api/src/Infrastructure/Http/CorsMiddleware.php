<?php
declare(strict_types=1);
namespace App\Infrastructure\Http;
use App\Infrastructure\Database;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
final class CorsMiddleware implements MiddlewareInterface {
 public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
  $allowed=array_filter(array_map('trim',explode(',',Database::env('CORS_ORIGINS',''))));$origin=$request->getHeaderLine('Origin');
  if($request->getMethod()==='OPTIONS'){ $response=new \Slim\Psr7\Response(); } else {$response=$handler->handle($request);}
  if($origin!==''&&in_array($origin,$allowed,true)){$response=$response->withHeader('Access-Control-Allow-Origin',$origin)->withHeader('Vary','Origin')->withHeader('Access-Control-Allow-Credentials','true');}
  return $response->withHeader('Access-Control-Allow-Methods','GET, POST, PUT, PATCH, DELETE, OPTIONS')->withHeader('Access-Control-Allow-Headers','Content-Type, Authorization, X-Request-Id');
 }
}
