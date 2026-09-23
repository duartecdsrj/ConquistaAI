<?php
declare(strict_types=1);

namespace App\Infrastructure;

use Psr\Http\Message\ResponseInterface;

final class JsonResponse
{
    public static function make(ResponseInterface $response, array $payload, int $status = 200): ResponseInterface
    {
        $response->getBody()->write((string) json_encode($payload, JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public static function error(ResponseInterface $response, string $code, string $message, int $status, array $details = []): ResponseInterface
    {
        return self::make($response, ['error' => compact('code', 'message', 'details')], $status);
    }
}
