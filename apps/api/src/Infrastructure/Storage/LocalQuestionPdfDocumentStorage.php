<?php
declare(strict_types=1);
namespace App\Infrastructure\Storage;
use App\Application\QuestionBank\Port\QuestionPdfDocumentStorageInterface;
final class LocalQuestionPdfDocumentStorage implements QuestionPdfDocumentStorageInterface { public function __construct(private readonly string $directory){} public function store(string $sha256,string $originalName,string $contents):string{if(!is_dir($this->directory)&&!mkdir($this->directory,0770,true)&&!is_dir($this->directory))throw new \RuntimeException('Armazenamento indisponivel.');$path=rtrim($this->directory,'/').'/'.$sha256.'.pdf';if(!is_file($path)&&file_put_contents($path,$contents,LOCK_EX)===false)throw new \RuntimeException('Nao foi possivel armazenar o PDF.');return $path;} }
