<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog;

use App\Application\Catalog\DTO\Request\CreateSubjectRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class SubjectRequestFactory
{
    public function create(ServerRequestInterface $request): CreateSubjectRequestDto
    {
        try { $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR); } catch (\JsonException) { throw new InvalidArgumentException('JSON invalido.'); }
        if (!is_array($payload) || array_is_list($payload) || !is_string($payload['syllabus_id'] ?? null) || !is_string($payload['name'] ?? null) || (($payload['parent_id'] ?? null) !== null && !is_string($payload['parent_id'])) || (($payload['source_excerpt'] ?? null) !== null && !is_string($payload['source_excerpt'])) || (($payload['source_page'] ?? null) !== null && !is_int($payload['source_page'])) || (($payload['source_start_offset'] ?? null) !== null && !is_int($payload['source_start_offset'])) || (($payload['source_end_offset'] ?? null) !== null && !is_int($payload['source_end_offset'])) || (($payload['sort_order'] ?? null) !== null && !is_int($payload['sort_order']))) throw new InvalidArgumentException('Campos de assunto invalidos.');
        return new CreateSubjectRequestDto($payload['syllabus_id'], $payload['parent_id'] ?? null, $payload['name'], $payload['sort_order'] ?? 0, $payload['source_excerpt'] ?? null, $payload['source_page'] ?? null, $payload['source_start_offset'] ?? null, $payload['source_end_offset'] ?? null);
    }
}
