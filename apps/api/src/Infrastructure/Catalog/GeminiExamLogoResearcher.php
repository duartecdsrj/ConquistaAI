<?php
declare(strict_types=1);

namespace App\Infrastructure\Catalog;

use App\Application\Catalog\Port\ExamLogoResearcherInterface;

final class GeminiExamLogoResearcher implements ExamLogoResearcherInterface
{
    public function __construct(private readonly string $apiKey, private readonly string $model)
    {
    }

    public function find(string $examName, ?string $organizer): array
    {
        if ($this->apiKey === '') {
            return ['institution' => null, 'organizer' => null];
        }

        $organizerName = $organizer === null || trim($organizer) === '' ? 'não informada' : trim($organizer);
        $prompt = 'Pesquise os domínios oficiais da instituição responsável pelo concurso e da banca organizadora. ' .
            'Concurso: ' . $examName . '. Banca: ' . $organizerName . '. ' .
            'Responda exclusivamente com JSON no formato {"institution_domain":"https://dominio-oficial","organizer_domain":"https://dominio-oficial-ou-null"}. ' .
            'Use apenas sites oficiais; se não houver certeza, use null.';
        $payload = ['tools' => [['google_search' => new \stdClass()]], 'generationConfig' => ['responseMimeType' => 'application/json'], 'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]]];
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($this->model) . ':generateContent?key=' . rawurlencode($this->apiKey);
        $context = stream_context_create(['http' => ['method' => 'POST', 'timeout' => 30, 'ignore_errors' => true, 'header' => "Content-Type: application/json\r\n", 'content' => json_encode($payload, JSON_THROW_ON_ERROR)]]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            return ['institution' => null, 'organizer' => null];
        }

        try {
            $response = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ['institution' => null, 'organizer' => null];
        }
        $parts = $response['candidates'][0]['content']['parts'] ?? [];
        $text = is_array($parts) ? trim(implode('', array_map(static fn (mixed $part): string => is_array($part) && is_string($part['text'] ?? null) ? $part['text'] : '', $parts))) : '';
        $text = trim(str_replace(['```json', '```'], '', $text));
        if ($text === '') {
            return ['institution' => null, 'organizer' => null];
        }
        try {
            $domains = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ['institution' => null, 'organizer' => null];
        }

        return ['institution' => $this->favicon($domains['institution_domain'] ?? null), 'organizer' => $this->favicon($domains['organizer_domain'] ?? null)];
    }

    private function favicon(mixed $candidate): ?string
    {
        if (!is_string($candidate)) {
            return null;
        }
        $host = parse_url(trim($candidate), PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return null;
        }
        return 'https://www.google.com/s2/favicons?domain=' . rawurlencode($host) . '&sz=128';
    }
}
