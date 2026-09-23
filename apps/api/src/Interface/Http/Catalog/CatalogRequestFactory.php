<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog;

use App\Application\Catalog\DTO\Request\CreateExamRequestDto;
use App\Application\Catalog\DTO\Request\UpdateExamRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class CatalogRequestFactory
{
    public function createExam(ServerRequestInterface $request): CreateExamRequestDto
    {
        [$name, $organizer, $year] = $this->examFields($request);

        return new CreateExamRequestDto($name, $organizer, $year);
    }

    public function updateExam(ServerRequestInterface $request, string $id): UpdateExamRequestDto
    {
        [$name, $organizer, $year] = $this->examFields($request);

        return new UpdateExamRequestDto($id, $name, $organizer, $year);
    }

    /** @return array{string, ?string, ?int} */
    private function examFields(ServerRequestInterface $request): array
    {
        $payload = $this->json($request);
        $name = $payload['name'] ?? null;
        $organizer = $payload['organizer'] ?? null;
        $year = $payload['year'] ?? null;

        if (!is_string($name) || ($organizer !== null && !is_string($organizer)) || ($year !== null && !is_int($year))) {
            throw new InvalidArgumentException('Campos de concurso invalidos.');
        }

        return [$name, $organizer, $year];
    }

    /** @return array<string, mixed> */
    private function json(ServerRequestInterface $request): array
    {
        try {
            $payload = json_decode((string) $request->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidArgumentException('JSON invalido.');
        }

        if (!is_object($payload)) {
            throw new InvalidArgumentException('O corpo deve ser um objeto JSON.');
        }

        return get_object_vars($payload);
    }
}
