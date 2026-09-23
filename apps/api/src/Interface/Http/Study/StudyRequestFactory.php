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
        $query = $request->getQueryParams();
        $page = $this->positiveInteger($query['page'] ?? 1, 'page');
        $perPage = $this->positiveInteger($query['per_page'] ?? 25, 'per_page');

        return new ListNotebooksRequestDto($page, $perPage);
    }

    public function createNotebook(ServerRequestInterface $request): CreateNotebookInputRequestDto
    {
        $payload = $this->json($request);
        $name = $payload['name'] ?? null;
        $mode = $payload['mode'] ?? null;
        $quantity = $payload['quantity'] ?? null;
        $questionIds = $payload['question_ids'] ?? null;

        if (
            !is_string($name)
            || !is_string($mode)
            || !in_array($mode, ['STUDY', 'EXAM'], true)
            || !is_int($quantity)
            || !is_array($questionIds)
            || !array_is_list($questionIds)
            || array_filter($questionIds, static fn (mixed $id): bool => !is_string($id)) !== []
        ) {
            throw new InvalidArgumentException('Campos de caderno invalidos.');
        }

        return new CreateNotebookInputRequestDto($name, $mode, $quantity, $questionIds);
    }

    private function positiveInteger(mixed $value, string $field): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }
        throw new InvalidArgumentException(sprintf('Parametro %s invalido.', $field));
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
