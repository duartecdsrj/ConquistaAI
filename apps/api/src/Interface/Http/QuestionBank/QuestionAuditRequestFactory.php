<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank;
use App\Application\QuestionBank\DTO\Request\ListQuestionAuditFindingsRequestDto;
use Psr\Http\Message\ServerRequestInterface;
final class QuestionAuditRequestFactory {
    public function findings(ServerRequestInterface $request): ListQuestionAuditFindingsRequestDto {
        $query = $request->getQueryParams();
        return new ListQuestionAuditFindingsRequestDto($this->integer($query['page'] ?? 1), $this->integer($query['per_page'] ?? 25));
    }
    private function integer(mixed $value): int {
        if (is_int($value)) return $value;
        if (is_string($value) && ctype_digit($value)) return (int) $value;
        throw new \InvalidArgumentException('Paginacao invalida.');
    }
}
