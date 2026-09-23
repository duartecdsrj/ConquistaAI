<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionBank;

use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
use App\Application\QuestionBank\DTO\Request\CommitQuestionImportRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class QuestionImportRequestFactory
{
    public function commit(ServerRequestInterface $request, string $userId, string $importId): CommitQuestionImportRequestDto
    {
        try { $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR); } catch (\JsonException) { throw new InvalidArgumentException("JSON invalido."); }
        if (!is_array($payload) || !is_string($payload["syllabus_id"] ?? null)) { throw new InvalidArgumentException("Informe syllabus_id."); }
        return new CommitQuestionImportRequestDto($userId, $importId, $payload["syllabus_id"]);
    }

    public function preview(ServerRequestInterface $request): PreviewQuestionImportRequestDto
    {
        try {
            $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidArgumentException('JSON invalido.');
        }

        if (!is_array($payload) || !is_string($payload['format'] ?? null) || !is_string($payload['content'] ?? null)) {
            throw new InvalidArgumentException('Informe format e content.');
        }

        return new PreviewQuestionImportRequestDto($payload['format'], $payload['content']);
    }
}
