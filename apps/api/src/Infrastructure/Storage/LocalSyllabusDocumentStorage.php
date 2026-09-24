<?php
declare(strict_types=1);
namespace App\Infrastructure\Storage;
use App\Application\Catalog\Port\SyllabusDocumentStorageInterface;
final class LocalSyllabusDocumentStorage implements SyllabusDocumentStorageInterface { public function __construct(private readonly string $directory){} public function store(string $sha256,string $originalName,string $contents):string{$extension=strtolower(pathinfo($originalName,PATHINFO_EXTENSION));$filename=$sha256.($extension!==''?'.'.$extension:'');$path=rtrim($this->directory,'/').'/'.$filename;if(!is_dir($this->directory)&&!mkdir($this->directory,0770,true)&&!is_dir($this->directory))throw new \RuntimeException('Diretorio de documentos indisponivel.');if(!is_file($path)&&file_put_contents($path,$contents,LOCK_EX)===false)throw new \RuntimeException('Nao foi possivel armazenar o documento.');return $path;} }
