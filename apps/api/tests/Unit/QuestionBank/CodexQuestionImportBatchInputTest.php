<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportEvidencePageDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportImageManifestItemDto;
use App\Infrastructure\QuestionBank\CodexQuestionImportAnalyzer;
use PHPUnit\Framework\TestCase;
final class CodexQuestionImportBatchInputTest extends TestCase
{
    public function testSeparatesImagePathsByCandidateFingerprint(): void
    {
        $workspace = sys_get_temp_dir()."/batch-input-".bin2hex(random_bytes(4)); mkdir($workspace, 0700, true);
        $first = tempnam(sys_get_temp_dir(), "image-"); $second = tempnam(sys_get_temp_dir(), "image-"); file_put_contents($first, "one"); file_put_contents($second, "two");
        try {
            $method = new \ReflectionMethod(CodexQuestionImportAnalyzer::class, "writeBatchInput");
            $firstInput = $method->invoke(new CodexQuestionImportAnalyzer(), $workspace, $this->candidate(str_repeat("a", 64), $first));
            $secondInput = $method->invoke(new CodexQuestionImportAnalyzer(), $workspace, $this->candidate(str_repeat("b", 64), $second));
            self::assertNotSame($firstInput["images"][0]["file"], $secondInput["images"][0]["file"]);
            self::assertFileExists($workspace."/".$firstInput["images"][0]["file"]);
            self::assertFileExists($workspace."/".$secondInput["images"][0]["file"]);
        } finally { @unlink($first); @unlink($second); foreach (glob($workspace."/images/*/*") ?: [] as $file) @unlink($file); foreach (glob($workspace."/images/*") ?: [] as $dir) @rmdir($dir); @rmdir($workspace."/images"); @rmdir($workspace); }
    }
    private function candidate(string $fingerprint, string $image): AnalyzeQuestionImportCandidateRequestDto { return new AnalyzeQuestionImportCandidateRequestDto($fingerprint, [new QuestionImportEvidencePageDto(1, "Enunciado")], [new QuestionImportImageManifestItemDto(1, 0, $image)], ["Tecnologia"]); }
}
