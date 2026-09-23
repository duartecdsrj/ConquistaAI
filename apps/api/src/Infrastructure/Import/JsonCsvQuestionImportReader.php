<?php
declare(strict_types=1);

namespace App\Infrastructure\Import;

use App\Application\QuestionBank\Port\QuestionImportReaderInterface;

final class JsonCsvQuestionImportReader implements QuestionImportReaderInterface
{
    public function read(string $format, string $content): array
    {
        return match ($format) {
            'JSON' => $this->readJson($content),
            'CSV' => $this->readCsv($content),
            default => throw new \InvalidArgumentException('Formato de importacao nao suportado.'),
        };
    }

    /** @return list<array<string, mixed>> */
    private function readJson(string $content): array
    {
        try {
            $rows = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Arquivo JSON invalido.');
        }

        if (!is_array($rows) || !array_is_list($rows)) {
            throw new \InvalidArgumentException('O arquivo JSON deve conter uma lista de questoes.');
        }
        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new \InvalidArgumentException('Cada item do JSON deve ser um objeto.');
            }
        }

        return $rows;
    }

    /** @return list<array<string, mixed>> */
    private function readCsv(string $content): array
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $header = fgetcsv($stream);
        if (!is_array($header) || $header === []) {
            throw new \InvalidArgumentException('Arquivo CSV sem cabecalho.');
        }
        $header = array_map(static fn (string $field): string => trim($field), $header);
        $required = ['statement', 'options', 'correct_option'];
        if (array_diff($required, $header) !== []) {
            throw new \InvalidArgumentException('CSV precisa conter statement, options e correct_option.');
        }

        $rows = [];
        while (($values = fgetcsv($stream)) !== false) {
            if ($values === [null] || $values === []) {
                continue;
            }
            $row = [];
            foreach ($header as $index => $field) {
                $row[$field] = $values[$index] ?? null;
            }
            if (is_string($row['options'] ?? null)) {
                try {
                    $row['options'] = json_decode($row['options'], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    // The validator reports an invalid alternatives value for this line.
                }
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
