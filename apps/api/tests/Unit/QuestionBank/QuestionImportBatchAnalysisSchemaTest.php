<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Application\QuestionBank\AI\QuestionImportAnalysisSchema;
use PHPUnit\Framework\TestCase;
final class QuestionImportBatchAnalysisSchemaTest extends TestCase
{
    public function testDefinesFingerprintBoundItems(): void
    {
        $schema = QuestionImportAnalysisSchema::batchJsonSchema();
        self::assertSame("question-import-analysis-batch-v1", $schema["properties"]["schema_version"]["const"]);
        self::assertSame(["candidate_fingerprint", "analysis"], $schema["properties"]["items"]["items"]["required"]);
        self::assertArrayHasKey("statement", $schema["properties"]["items"]["items"]["properties"]["analysis"]["properties"]);
    }
}
