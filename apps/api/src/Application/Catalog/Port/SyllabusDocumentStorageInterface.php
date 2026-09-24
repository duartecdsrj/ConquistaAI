<?php
declare(strict_types=1);
namespace App\Application\Catalog\Port;
interface SyllabusDocumentStorageInterface { public function store(string $sha256,string $originalName,string $contents):string; }
