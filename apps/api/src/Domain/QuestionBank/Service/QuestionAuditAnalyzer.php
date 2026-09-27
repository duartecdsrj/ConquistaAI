<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Service;

/** Deterministic, non-mutating inspection. A finding never authorizes rewriting content. */
final class QuestionAuditAnalyzer
{
    /** @param list<string> $options @return list<array{code:string,confidence:string,message:string}> */
    public function inspect(string $statement, array $options, int $assetCount = 0): array
    {
        $findings = [];
        $statement = trim($statement);
        if ($statement === '') $findings[] = $this->finding('EXTRACAO_INCOMPLETA', 'HIGH', 'Enunciado vazio.');
        if (!in_array(count($options), [4, 5], true)) $findings[] = $this->finding('EXTRACAO_INCOMPLETA', 'HIGH', 'A questão não possui quatro ou cinco alternativas.');

        $normalized = array_map($this->normalize(...), $options);
        if (count($normalized) !== count(array_filter($normalized))) $findings[] = $this->finding('EXTRACAO_INCOMPLETA', 'HIGH', 'Há alternativa vazia.');
        if (count(array_unique($normalized)) !== count($normalized)) $findings[] = $this->finding('POSSIVEL_DUPLICATA', 'HIGH', 'Há alternativas duplicadas.');
        if (preg_match('/(?:^|\n)\s*[A-E][.)]\s+.{1,}(?:\n|\s+)[B-E][.)]\s+/u', $statement) === 1) $findings[] = $this->finding('POSSIVEL_QUESTAO_MESCLADA', 'HIGH', 'Alternativas aparecem dentro do enunciado.');
        if (preg_match('/(?:^|\n)\s*(?:Quest[aã]o\s+)?\d{1,3}\s*[.)].{0,200}(?:\n|\s+)(?:Quest[aã]o\s+)?\d{1,3}\s*[.)]/iu', $statement) === 1) $findings[] = $this->finding('POSSIVEL_QUESTAO_MESCLADA', 'MEDIUM', 'Há mais de um marcador de questão no enunciado.');
        if ($this->hasDocumentBodyLeak($statement)) $findings[] = $this->finding('CONTEUDO_DOCUMENTAL_MESCLADO', 'HIGH', 'O enunciado contém sumário, gabarito ou texto documental alheio à questão; exige comparação com o PDF original.');
        if (str_contains($statement, '�') || array_filter($options, static fn (string $option): bool => str_contains($option, '�')) !== []) $findings[] = $this->finding('CARACTERE_CORROMPIDO', 'HIGH', 'Há caractere corrompido pela extração; o conteúdo exige comparação com o PDF original.');
        if ($this->referencesVisual($statement) && $assetCount === 0) $findings[] = $this->finding('IMAGEM_NAO_LOCALIZADA', 'MEDIUM', 'O enunciado referencia elemento visual sem asset associado.');
        if ($this->hasExtractionNoise($statement)) $findings[] = $this->finding('RUIDO_EXTRACAO', 'HIGH', 'Há cabeçalho, rodapé ou metadado de extração no enunciado.');
        if ($this->looksLikeCode($statement) || array_filter($options, $this->looksLikeCode(...)) !== []) $findings[] = $this->finding('CODIGO_SEM_BLOCO', 'MEDIUM', 'Há código sem bloco delimitado para apresentação segura.');
        if ($this->looksLikeCorrelation($statement)) $findings[] = $this->finding('ESTRUTURA_CORRELACAO', 'MEDIUM', 'Há estrutura de correlação que requer apresentação em colunas.');

        $unique = []; foreach ($findings as $finding) $unique[$finding['code']] ??= $finding; return array_values($unique);
    }

    private function finding(string $code, string $confidence, string $message): array { return compact('code', 'confidence', 'message'); }
    private function normalize(string $value): string { return mb_strtolower((string) preg_replace('/\s+/u', ' ', trim($value))); }
    private function referencesVisual(string $value): bool { return preg_match('/\b(?:figura|imagem|gr[aá]fico|tabela|quadro|diagrama|esquema|mapa|fluxograma)\b/iu', $value) === 1; }
    private function hasDocumentBodyLeak(string $value): bool { return preg_match('/(?:^|\n)\s*(?:[Ii]ndice|Sum.rio|REFER.NCIAS)\b|\bGABARITO\s*\n\s*\d+[.)]/iu', $value) === 1; }
    private function hasExtractionNoise(string $value): bool { return preg_match('/(?:www\.|p[aá]gina\s+\d+|licen[cs]ed to|concursos?\s+da\s+|equipe\s+inform[aá]tica)/iu', $value) === 1; }
    private function looksLikeCode(string $value): bool { return !str_contains($value, '```') && preg_match('/(?:^|\n)\s*(?:SELECT\b|INSERT\b|UPDATE\b|DELETE\b|CREATE\b|function\b|class\b|(?:const|let|var|public|private|static|def|import|from)\b|if\s*\(|for\s*\(|while\s*\(|\$[A-Za-z_]\w*\s*=|#!|(?:curl|grep|chmod|chown|git|docker|npm|composer|php|python3?|node|kubectl)\b(?:\s|$)|<\/?[a-z]|\{\s*$)/imu', $value) === 1; }
    private function looksLikeCorrelation(string $value): bool { return preg_match('/\b(?:correlacione|relacione|associe|ligue\s+as\s+colunas)\b/iu', $value) === 1 || (preg_match_all('/(?:^|\n)\s*(?:\d+|[IVXLCDM]+|[A-Z])[.)]\s+/mu', $value) >= 2 && substr_count($value, '( )') >= 2); }
}
