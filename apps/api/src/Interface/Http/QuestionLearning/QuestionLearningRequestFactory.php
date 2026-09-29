<?php
declare(strict_types=1);

namespace App\Interface\Http\QuestionLearning;

use App\Application\QuestionLearning\DTO\Request\CreateQuestionCommentRequestDto;
use App\Application\QuestionLearning\DTO\Request\CreateQuestionProblemReportRequestDto;
use App\Application\QuestionLearning\DTO\Request\RequestQuestionExplanationRequestDto;
use App\Application\QuestionLearning\DTO\Request\SaveQuestionNoteRequestDto;
use App\Application\QuestionLearning\DTO\Request\UpdateQuestionInteractionRequestDto;
use App\Domain\QuestionLearning\Enum\ProblemReportCategory;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class QuestionLearningRequestFactory
{
    public function explanation(string $questionId, ServerRequestInterface $request): RequestQuestionExplanationRequestDto
    {
        return new RequestQuestionExplanationRequestDto($questionId, $this->optionalString($this->body($request), 'attempt_id'));
    }

    public function interaction(string $questionId, ServerRequestInterface $request): UpdateQuestionInteractionRequestDto
    {
        $body = $this->body($request);
        return new UpdateQuestionInteractionRequestDto($questionId, $this->optionalBoolean($body, 'favorite'), $this->optionalBoolean($body, 'review_later'), $this->optionalBoolean($body, 'not_mastered'));
    }

    public function note(string $questionId, ServerRequestInterface $request): SaveQuestionNoteRequestDto
    {
        return new SaveQuestionNoteRequestDto($questionId, $this->requiredString($this->body($request), 'content'));
    }

    public function comment(string $questionId, ServerRequestInterface $request): CreateQuestionCommentRequestDto
    {
        $body = $this->body($request);
        return new CreateQuestionCommentRequestDto($questionId, $this->requiredString($body, 'content'), $this->optionalString($body, 'parent_id'));
    }

    public function report(string $questionId, ServerRequestInterface $request): CreateQuestionProblemReportRequestDto
    {
        $body = $this->body($request);
        try {
            return new CreateQuestionProblemReportRequestDto($questionId, ProblemReportCategory::from($this->requiredString($body, 'category')), $this->requiredString($body, 'description'));
        } catch (\ValueError) {
            throw new InvalidArgumentException('Categoria inválida.');
        }
    }

    /** @return array<string, mixed> */
    private function body(ServerRequestInterface $request): array
    {
        try {
            $payload = json_decode((string) $request->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidArgumentException('JSON inválido.');
        }
        if (!is_object($payload)) {
            throw new InvalidArgumentException('JSON inválido.');
        }
        return get_object_vars($payload);
    }

    /** @param array<string, mixed> $body */
    private function requiredString(array $body, string $field): string
    {
        $value = $body[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException("Campo {$field} inválido.");
        }
        return trim($value);
    }

    /** @param array<string, mixed> $body */
    private function optionalString(array $body, string $field): ?string
    {
        $value = $body[$field] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException("Campo {$field} inválido.");
        }
        return trim($value);
    }

    /** @param array<string, mixed> $body */
    private function optionalBoolean(array $body, string $field): ?bool
    {
        $value = $body[$field] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_bool($value)) {
            throw new InvalidArgumentException("Campo {$field} inválido.");
        }
        return $value;
    }
}
