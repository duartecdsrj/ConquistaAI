<?php
declare(strict_types=1);
namespace App\Application\Catalog\Port;
interface PdfTextExtractorInterface { /** @return list<string> */ public function extractPages(string $path):array; }
