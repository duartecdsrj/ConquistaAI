<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;

final class ApiResponseFactory
{
    public function success(ResponseInterface $response, mixed $data, string $requestId, int $status = 200): ResponseInterface
    {
        return $this->json($response, ['data' => $data, 'meta' => ['request_id' => $requestId]], $status, $requestId);
    }

    public function paginated(
        ResponseInterface $response,
        array $data,
        string $requestId,
        int $page,
        int $perPage,
        int $total,
    ): ResponseInterface {
        return $this->json($response, [
            'data' => $data,
            'meta' => [
                'request_id' => $requestId,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $total === 0 ? 0 : (int) ceil($total / $perPage),
                ],
            ],
        ], 200, $requestId);
    }

    public function problem(ResponseInterface $response, string $code, string $message, int $status, string $requestId, array $details = []): ResponseInterface
    {
        return $this->json($response, ['error' => ['code' => $code, 'message' => $message, 'details' => $details], 'meta' => ['request_id' => $requestId]], $status, $requestId);
    }

    private function json(ResponseInterface $response, array $payload, int $status, string $requestId): ResponseInterface
    {
        $response->getBody()->write((string) json_encode($payload, JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json')->withHeader('X-Request-Id', $requestId)->withStatus($status);
    }
}
