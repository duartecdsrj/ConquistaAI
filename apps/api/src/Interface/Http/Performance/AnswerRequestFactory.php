<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class AnswerRequestFactory
{
    /** @return array{optionId:?string,elapsedSeconds:int} */
    public function append(ServerRequestInterface $request): array
    {
        try {
            $payload = json_decode((string) $request->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidArgumentException('JSON invalido.');
        }
        if (!is_object($payload)) {
            throw new InvalidArgumentException('O corpo deve ser um objeto JSON.');
        }
        $values = get_object_vars($payload);
        $optionId = $values['option_id'] ?? null;
        $elapsedSeconds = $values['elapsed_seconds'] ?? null;
        if (($optionId !== null && !is_string($optionId)) || !is_int($elapsedSeconds) || $elapsedSeconds < 0) {
            throw new InvalidArgumentException('Campos de resposta invalidos.');
        }

        return ['optionId' => $optionId, 'elapsedSeconds' => $elapsedSeconds];
    }
}
