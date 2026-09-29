<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionBank;

use App\Application\QuestionBank\DTO\Request\ListPublishedQuestionsRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class PublishedQuestionRequestFactory
{
    public function list(ServerRequestInterface $request): ListPublishedQuestionsRequestDto
    {
        $query = $request->getQueryParams();
        $page = $this->integer($query['page'] ?? 1, 'page');
        $perPage = $this->integer($query['per_page'] ?? 25, 'per_page');
        $year = isset($query['year']) ? $this->integer($query['year'], 'year') : null;
        $subjectId = $this->nullableString($query['subject_id'] ?? null, 'subject_id');
        $board = $this->nullableString($query['board'] ?? null, 'board');
        $difficulty = $this->nullableString($query['difficulty'] ?? null, 'difficulty');

        $content = $this->nullableString($query['content'] ?? null, 'content');
        return new ListPublishedQuestionsRequestDto($page, $perPage, $subjectId, $board, $year, $difficulty, $content, $this->nullableBoolean($query['favorite'] ?? null, 'favorite'), $this->nullableBoolean($query['review_later'] ?? null, 'review_later'), $this->nullableBoolean($query['not_mastered'] ?? null, 'not_mastered'));
    }

    private function integer(mixed $value, string $field): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }
        throw new InvalidArgumentException(sprintf('Parametro %s invalido.', $field));
    }

    private function nullableBoolean(mixed $value, string $field): ?bool { if ($value === null || $value === '') return null; if ($value === 'true' || $value === true || $value === '1') return true; if ($value === 'false' || $value === false || $value === '0') return false; throw new InvalidArgumentException(sprintf('Parametro %s invalido.', $field)); }

    private function nullableString(mixed $value, string $field): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Parametro %s invalido.', $field));
        }
        return $value;
    }
}
