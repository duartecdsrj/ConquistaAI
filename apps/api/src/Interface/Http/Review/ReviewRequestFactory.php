<?php
declare(strict_types=1);

namespace App\Interface\Http\Review;

use App\Application\Review\DTO\Request\RateFlashcardInputRequestDto;
use App\Domain\Review\Enum\ReviewRating;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class ReviewRequestFactory
{
    public function navigation(ServerRequestInterface $request): string { try{$payload=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new InvalidArgumentException('JSON inválido.');} $direction=is_array($payload)?$payload['direction']??null:null; if(!is_string($direction)||!in_array($direction,['NEXT','PREVIOUS'],true))throw new InvalidArgumentException('Direção inválida.');return $direction; }
    public function page(ServerRequestInterface $request): int { return $this->positiveInteger($request->getQueryParams()['page'] ?? 1, 'page'); }
    public function perPage(ServerRequestInterface $request): int { $perPage = $this->positiveInteger($request->getQueryParams()['per_page'] ?? 25, 'per_page'); if ($perPage > 100) throw new InvalidArgumentException('Parâmetro per_page inválido.'); return $perPage; }
    public function limit(ServerRequestInterface $request, int $default): int { $limit = $this->positiveInteger($request->getQueryParams()['limit'] ?? $default, 'limit'); if ($limit > 100) throw new InvalidArgumentException('Parâmetro limit inválido.'); return $limit; }
    public function rate(ServerRequestInterface $request, string $sessionId, string $cardId): RateFlashcardInputRequestDto
    {
        if ($sessionId === '' || $cardId === '') throw new InvalidArgumentException('Identificadores de revisão inválidos.');
        try { $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR); } catch (\JsonException) { throw new InvalidArgumentException('JSON inválido.'); }
        if (!is_array($payload) || array_is_list($payload) || !is_string($payload['rating'] ?? null)) throw new InvalidArgumentException('Classificação inválida.');
        try { $rating = ReviewRating::from($payload['rating']); } catch (\ValueError) { throw new InvalidArgumentException('Classificação inválida.'); }
        return new RateFlashcardInputRequestDto($sessionId, $cardId, $rating);
    }
    private function positiveInteger(mixed $value, string $field): int { if (is_int($value) && $value > 0) return $value; if (is_string($value) && ctype_digit($value) && (int) $value > 0) return (int) $value; throw new InvalidArgumentException(sprintf('Parâmetro %s inválido.', $field)); }
}
