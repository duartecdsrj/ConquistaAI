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
            'required' => ['schema_version', 'statement', 'options', 'structure_type', 'metadata', 'evidence_pages', 'taxonomy_path', 'parent_subject', 'difficulty', 'correct_option', 'answer_key_assessment', 'image_assessment', 'findings', 'image_anchors'],
            'properties' => [
                'schema_version' => ['type' => 'string', 'const' => 'question-import-analysis-v1'],
                'statement' => ['type' => 'string', 'minLength' => 1],
                'options' => ['type' => 'array', 'minItems' => 4, 'maxItems' => 5, 'items' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['label', 'content'], 'properties' => ['label' => ['type' => 'string', 'pattern' => '^[A-E]$'], 'content' => ['type' => 'string', 'minLength' => 1]]]],
                'structure_type' => ['type' => 'string', 'enum' => ['MULTIPLE_CHOICE', 'MATCHING', 'ASSERTIONS']],
                'metadata' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['exam', 'position', 'board', 'year', 'evidence_pages'], 'properties' => [
                    'exam' => ['type' => ['string', 'null']], 'position' => ['type' => ['string', 'null']], 'board' => ['type' => ['string', 'null']], 'year' => ['type' => ['integer', 'null']],
                    'evidence_pages' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['exam', 'position', 'board', 'year'], 'properties' => [
                        'exam' => ['type' => 'array', 'items' => ['type' => 'integer', 'minimum' => 1]], 'position' => ['type' => 'array', 'items' => ['type' => 'integer', 'minimum' => 1]],
                        'board' => ['type' => 'array', 'items' => ['type' => 'integer', 'minimum' => 1]], 'year' => ['type' => 'array', 'items' => ['type' => 'integer', 'minimum' => 1]],
                    ]],
                ]],
                'evidence_pages' => ['type' => 'array', 'minItems' => 1, 'items' => ['type' => 'integer', 'minimum' => 1]],
                'taxonomy_path' => ['type' => 'array', 'minItems' => 1, 'items' => ['type' => 'string', 'minLength' => 1]],
                'parent_subject' => ['type' => ['string', 'null']],
                'difficulty' => ['type' => 'string', 'enum' => ['EASY', 'MEDIUM', 'HARD']],
                'correct_option' => ['type' => ['string', 'null']],
                'answer_key_assessment' => ['type' => 'string', 'enum' => ['CONSISTENT', 'CONFLICT', 'UNKNOWN']],
                'image_assessment' => ['type' => 'string', 'enum' => ['COHERENT', 'DISCREPANCY', 'NOT_APPLICABLE']],
                'findings' => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['code', 'severity', 'confidence', 'safe_summary', 'evidence_pages'], 'properties' => [
                    'code' => ['type' => 'string', 'enum' => ['ANSWER_KEY_CONFLICT', 'IMAGE_DISCREPANCY', 'STRUCTURAL_INCONSISTENCY', 'METADATA_UNVERIFIED']],
                    'severity' => ['type' => 'string', 'enum' => ['INFO', 'WARNING', 'CRITICAL']], 'confidence' => ['type' => ['number', 'null'], 'minimum' => 0, 'maximum' => 1],
                    'safe_summary' => ['type' => 'string', 'minLength' => 1], 'evidence_pages' => ['type' => 'array', 'minItems' => 1, 'items' => ['type' => 'integer', 'minimum' => 1]],
                ]]],
                'image_anchors' => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['target', 'option_label', 'source_page', 'source_asset_index', 'status'], 'properties' => [
                    'target' => ['type' => 'string', 'enum' => ['STATEMENT', 'OPTION']], 'option_label' => ['type' => ['string', 'null']],
                    'source_page' => ['type' => 'integer', 'minimum' => 1], 'source_asset_index' => ['type' => ['integer', 'null'], 'minimum' => 0],
                    'status' => ['type' => 'string', 'enum' => ['ANCHORED', 'DISCREPANCY', 'AMBIGUOUS']],
                ]]],
            ],
        ];
    }

    /**  array<string,mixed> */
    public static function batchJsonSchema(): array
    {
        return ["type" => "object", "additionalProperties" => false, "required" => ["schema_version", "items"], "properties" => ["schema_version" => ["type" => "string", "const" => "question-import-analysis-batch-v1"], "items" => ["type" => "array", "minItems" => 1, "items" => ["type" => "object", "additionalProperties" => false, "required" => ["candidate_fingerprint", "analysis"], "properties" => ["candidate_fingerprint" => ["type" => "string"], "analysis" => self::jsonSchema()]]]]];    }
}
