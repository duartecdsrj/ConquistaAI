<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Service;
final class ImportedQuestionContentSanitizer
{
    /** @param list<array{content?: mixed}> $options */
    public function statement(string $statement, array $options): string
    {
        $clean = $this->text($statement);
        $clean = trim((string) (preg_replace('/^\s*(?:Ano|Banca|Concurso|Cargo|N[íi]vel)\s*:[^\n]*\R+/iu', '', $clean) ?? $clean));
        $clean = trim((string) (preg_replace('/^\s*Concurso:\s*.*?\bN[íi]vel:\s*(?:Fundamental|M[eé]dio|Superior)\s*/iu', '', $clean) ?? $clean));
        $clean = trim((string) (preg_replace('/^\s*\d{1,3}\s*[.)]\s+/u', '', $clean) ?? $clean));
        if (count($options) < 3) return $clean;
        if (preg_match('/(?:^|[\s\n])([Aa])\s*[.)]\s+.+?(?:[\s\n])([Bb])\s*[.)]\s+.+?(?:[\s\n])([Cc])\s*[.)]\s+/su', $clean, $matches, PREG_OFFSET_CAPTURE) === 1) {
            return trim(substr($clean, 0, $matches[1][1]));
        }
        return $clean;
    }

    public function option(string $content): string
    {
        return trim((string) preg_replace('/^\s*(?:[A-Ea-e]\s*[.)\-:]\s*)+/u', '', $this->text($content)));
    }

    /**
     * Normaliza artefatos do pdftotext sem alterar o conteúdo semântico da questão.
     * Alguns PDFs inserem rodapés no meio da alternativa e os serializam como "\\n".
     */
    public function text(string $content): string
    {
        $clean = str_replace(["\r\n", "\r"], "\n", $content);
        // Não converte caminhos como "\\net\\web": somente sequências usadas como quebra.
        $clean = (string) (preg_replace('/\\\\r?\\\\n/u', "\n", $clean) ?? $clean);
        $clean = (string) (preg_replace('/\\\\n(?!et(?:[\\\\\/]|$)|etwork(?:[\\\\\/]|$))/iu', "\n", $clean) ?? $clean);
        $clean = (string) (preg_replace('/\n\s*(?:Coment[aá]rios?|Resolu[cç][aã]o|Gabarito)\s*:\s*[\s\S]*$/iu', '', $clean) ?? $clean);
        $clean = (string) (preg_replace('/(?im)^.*(?:Concursos da [^\n]*\d+|(?:Evandro Dalla Vecchia|Andr[eé] Castro), Equipe Informática e TI(?:, Marcos Vin[ií]cius Alves Franco)?|www\.estrategiaconcursos\.com\.br|Eletronica Em Arte|Licensed to [^\n]*|==[0-9a-f]{6,}==).*(?:\n|$)/u', '', $clean) ?? $clean);

        $lines = [];
        $previousBlank = false;
        foreach (explode("\n", $clean) as $line) {
            $line = trim($line);
            if (preg_match('/^\d{1,3}$/u', $line) === 1) continue;
            if ($line === '') {
                if (!$previousBlank) $lines[] = '';
                $previousBlank = true;
                continue;
            }
            $lines[] = $line;
            $previousBlank = false;
        }
        return trim(implode("\n", $lines));
    }
}
