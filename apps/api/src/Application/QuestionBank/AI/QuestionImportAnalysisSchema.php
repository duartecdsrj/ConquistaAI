<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\AI;

final class QuestionImportAnalysisSchema
{
    /** @return array<string,mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['schema_version', 'statement', 'options', 'structure_type', 'metadata', 'evidence_pages', 'taxonomy_path', 'difficulty', 'correct_option', 'answer_key_assessment', 'image_assessment', 'findings', 'image_anchors'],
            'properties' => [
                'schema_version' => ['const' => 'question-import-analysis-v1'],
                'statement' => ['type' => 'string', 'minLength' => 1],
                'options' => ['type' => 'array', 'minItems' => 4, 'maxItems' => 5, 'items' => ['type' => 'object', 'required' => ['label', 'content'], 'properties' => ['label' => ['type' => 'string', 'pattern' => '^[A-E]$'], 'content' => ['type' => 'string', 'minLength' => 1]]]],
                'structure_type' => ['enum' => ['MULTIPLE_CHOICE', 'MATCHING', 'ASSERTIONS']],
                'metadata' => ['type' => 'object', 'required' => ['exam', 'position', 'board', 'year', 'evidence_pages']],
                'evidence_pages' => ['type' => 'array', 'minItems' => 1, 'items' => ['type' => 'integer', 'minimum' => 1]],
                'taxonomy_path' => ['type' => 'array', 'minItems' => 1, 'items' => ['type' => 'string', 'minLength' => 1]],
                'parent_subject' => ['type' => ['string', 'null']],
                'difficulty' => ['enum' => ['EASY', 'MEDIUM', 'HARD']],
                'correct_option' => ['type' => ['string', 'null']],
                'answer_key_assessment' => ['enum' => ['CONSISTENT', 'CONFLICT', 'UNKNOWN']],
                'image_assessment' => ['enum' => ['COHERENT', 'DISCREPANCY', 'NOT_APPLICABLE']],
                'findings' => ['type' => 'array'],
                'image_anchors' => ['type' => 'array'],
            ],
        ];
    }
}
