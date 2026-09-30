<?php
declare(strict_types=1);

namespace Tests\Integration\QuestionBank;

use App\Application\QuestionBank\Service\QuestionPdfCandidateSegmenter;
use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use PHPUnit\Framework\TestCase;

final class QuestionPdfCandidateSegmentationRegressionTest extends TestCase
{
    public function testReferencePdfCorpusHasSixtySixMultipleChoiceCandidates(): void
    {
        $directory = getenv('QUESTION_IMPORT_REGRESSION_PDF_DIR') ?: '/app/storage/question-pdfs';
        $expected = [
            '8e24fad6e39af383efc14eaf9df7245faa5f025eedb5a0f8393a1aa2f993400a.pdf' => 2,
            '3c860628daa05a3306ec33a1622dcf5038f3c30c3b512bc0293ef953c1735885.pdf' => 25,
            '10c3ef1b74a899535900cccfd3a40368a70c39f203fdcc57089b507a4dd5366b.pdf' => 2,
            'aa48eb23b05fd0c939a0a7cd492e097511160f45f0d7831dd56bfdf9c1bb2a2e.pdf' => 15,
            '8b5c33e0703d4f94e05bfa13913d5d61f3b9bbf112067d2c64d8d427c59451c6.pdf' => 22,
        ];
        foreach (array_keys($expected) as $filename) if (!is_file($directory.'/'.$filename)) self::markTestSkipped('Corpus de regressão PDF não está montado.');
        $extractor = new PdftotextPdfTextExtractor();
        $segmenter = new QuestionPdfCandidateSegmenter();
        $total = 0;
        foreach ($expected as $filename => $count) {
            $candidates = $segmenter->segment($extractor->extractPages($directory.'/'.$filename), [], []);
            self::assertCount($count, $candidates, $filename);
            $total += count($candidates);
        }
        self::assertSame(66, $total);
    }
}
