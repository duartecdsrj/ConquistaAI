<?php
declare(strict_types=1);

namespace App\Interface\Http\Catalog;

use App\Application\Catalog\DTO\Request\AssignPositionTaxonomySubjectsRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class PositionTaxonomyRequestFactory
{
    public function assign(ServerRequestInterface $request, string $positionId): AssignPositionTaxonomySubjectsRequestDto
    {
        try { $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new InvalidArgumentException('JSON inválido.'); }
        if (!is_array($payload) || array_is_list($payload) || !is_array($payload['taxonomy_subject_ids'] ?? null) || array_filter($payload['taxonomy_subject_ids'], static fn ($id): bool => !is_string($id) || trim($id) === '') !== []) throw new InvalidArgumentException('Assuntos canônicos inválidos.');
        return new AssignPositionTaxonomySubjectsRequestDto($positionId, array_values($payload['taxonomy_subject_ids']));
    }
}
