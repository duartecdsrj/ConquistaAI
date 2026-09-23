<?php
declare(strict_types=1);

namespace App\Interface\Http\Study;

use App\Application\Study\DTO\Request\CreateNotebookInputRequestDto;
use App\Application\Study\DTO\Request\ListNotebooksRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class StudyRequestFactory
{
    public function listNotebooks(ServerRequestInterface $request): ListNotebooksRequestDto
    {
        return new ListNotebooksRequestDto($this->page($request), $this->perPage($request));
    }

    public function page(ServerRequestInterface $request): int
    {
        return $this->positiveInteger($request->getQueryParams()['page'] ?? 1, 'page');
    }

    public function perPage(ServerRequestInterface $request): int
    {
        return $this->positiveInteger($request->getQueryParams()['per_page'] ?? 25, 'per_page');
    }

    public function createNotebook(ServerRequestInterface $request): CreateNotebookInputRequestDto
    {
        $payload = $this->json($request);
        $name = $payload['name'] ?? null;
        $mode = $payload['mode'] ?? null;
        $quantity = $payload['quantity'] ?? null;
        $filters = $payload['filters'] ?? [];
        if (!is_string($name) || !is_string($mode) || !in_array($mode, ['STUDY', 'EXAM'], true) || !is_int($quantity) || !is_array($filters) || !$this->validFilters($filters)) {
            throw new InvalidArgumentException('Campos de caderno invalidos.');
        }
        return new CreateNotebookInputRequestDto($name, $mode, $quantity, $filters);
    }

    private function positiveInteger(mixed $value, string $field): int
    {
        if (is_int($value)) return $value;
        if (is_string($value) && ctype_digit($value)) return (int) $value;
        throw new InvalidArgumentException(sprintf('Parametro %s invalido.', $field));
    }

    /** @param array<string, mixed> $filters */
    private function validFilters(array $filters): bool
    {
        $allowed = ['subject_id', 'board', 'year', 'difficulty'];
        if (array_diff(array_keys($filters), $allowed) !== []) return false;
        return (!isset($filters['subject_id']) || is_string($filters['subject_id']))
            && (!isset($filters['board']) || is_string($filters['board']))
            && (!isset($filters['year']) || is_int($filters['year']))
            && (!isset($filters['difficulty']) || (is_string($filters['difficulty']) && in_array($filters['difficulty'], ['EASY', 'MEDIUM', 'HARD'], true)));
    }

    /** @return array<string, mixed> */
    private function json(ServerRequestInterface $request): array
    {
        try { $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new InvalidArgumentException('JSON invalido.'); }
        if (!is_array($payload) || array_is_list($payload)) throw new InvalidArgumentException('O corpo deve ser um objeto JSON.');
        return $payload;
    }
}
