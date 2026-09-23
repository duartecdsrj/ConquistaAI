<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestIdMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestId = $request->getHeaderLine('X-Request-Id');
        if (!preg_match('/^[A-Za-z0-9._-]{1,128}$/', $requestId)) {
            $requestId = bin2hex(random_bytes(16));
        }

        return $handler->handle($request->withAttribute('request_id', $requestId))
            ->withHeader('X-Request-Id', $requestId);
    }
}
