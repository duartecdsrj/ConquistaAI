<?php
declare(strict_types=1);

namespace App\Infrastructure\Extraction;

use App\Application\QuestionBank\Port\QuestionPdfAssetExtractorInterface;

final class PdfimagesQuestionPdfAssetExtractor implements QuestionPdfAssetExtractorInterface
{
    public function __construct(private readonly string $directory = "/app/storage/question-pdf-assets") {}

    /** @param list<int> $requestedPages @return array<int,list<string>> */
    public function extract(string $pdf, string $hash, array $requestedPages = []): array
    {
        $wanted = array_values(array_unique(array_filter(array_map("intval", $requestedPages), static fn (int $page): bool => $page > 0)));
        if ($requestedPages !== [] && $wanted === []) return [];
        $list = shell_exec("pdfimages -list ".escapeshellarg($pdf)) ?? "";
        $pages = array_fill_keys($wanted, true);
        foreach (explode("\n", $list) as $line) {
            if (preg_match("/^\s*(\d+)\s+(\d+)\s+image\s+(\d+)\s+(\d+)/", $line, $match) !== 1 || (int) $match[3] <= 30 || (int) $match[4] <= 30) continue;
            $page = (int) $match[1];
            if ($wanted !== [] && !in_array($page, $wanted, true)) continue;
            $pages[$page] = true;
        }
        if ($pages === []) return [];
        $dir = $this->directory."/".$hash;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) throw new \RuntimeException("Diretório de imagens indisponível.");
        $result = [];
        foreach (array_keys($pages) as $page) {
            $prefix = sprintf("%s/page-%d", $dir, $page);
            $existing = glob($prefix."-*.png") ?: [];
            if ($existing === []) {
                shell_exec("pdfimages -f ".(int) $page." -l ".(int) $page." -png ".escapeshellarg($pdf)." ".escapeshellarg($prefix));
                $existing = glob($prefix."-*.png") ?: [];
            }
            $renderPrefix = sprintf("%s/page-%d-render", $dir, $page);
            if (!is_file($renderPrefix.".png")) shell_exec("pdftoppm -f ".(int) $page." -l ".(int) $page." -r 144 -png -singlefile ".escapeshellarg($pdf)." ".escapeshellarg($renderPrefix));
            $existing = glob($prefix."-*.png") ?: [];
            usort($existing, static fn (string $left, string $right): int => strcmp($left, $right));
            foreach ($existing as $path) if (is_file($path) && (filesize($path) ?: 0) > 1024) $result[$page][] = $path;
        }
        return $result;
    }
}
