<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Port;
interface QuestionPdfDocumentStorageInterface { public function store(string $sha256,string $originalName,string $contents):string; }
