<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Infrastructure\Http\ApiResponseFactory;
use App\Infrastructure\Http\CorsMiddleware;
use App\Infrastructure\Http\RequestIdMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory as SlimAppFactory;
use Slim\App;

final class AppFactory
{
    public static function create(): App
    {
        $app = SlimAppFactory::create();
        $responses = new ApiResponseFactory();

        $app->get('/health', static function (ServerRequestInterface $request, ResponseInterface $response) use ($responses): ResponseInterface {
            return $responses->success($response, ['status' => 'ok'], (string) $request->getAttribute('request_id'));
        });

        $app->add(new RequestIdMiddleware());
        $app->add(new CorsMiddleware());
        $app->addErrorMiddleware((bool) Database::env('APP_DEBUG', 'false'), true, true);

        return $app;
    }
}
