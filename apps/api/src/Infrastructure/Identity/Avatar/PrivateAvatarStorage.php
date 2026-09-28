<?php
declare(strict_types=1);
namespace App\Infrastructure\Identity\Avatar;
use App\Application\Identity\Port\AvatarStorageInterface;
use App\Application\Identity\Port\StoredAvatar;
final class PrivateAvatarStorage implements AvatarStorageInterface
{
    public function __construct(private readonly string $directory = '/app/storage/avatars') {}
    public function store(string $userId, string $contents, string $mimeType): StoredAvatar { if(!in_array($mimeType,['image/jpeg','image/png','image/webp'],true))throw new \InvalidArgumentException('Formato de imagem inválido.');if(!is_dir($this->directory)&&!mkdir($this->directory,0700,true)&&!is_dir($this->directory))throw new \RuntimeException('Não foi possível preparar o armazenamento de avatar.');$key=$userId.'/'.bin2hex(random_bytes(16)).'.webp';$path=$this->path($key);$directory=dirname($path);if(!is_dir($directory)&&!mkdir($directory,0700,true)&&!is_dir($directory))throw new \RuntimeException('Não foi possível preparar o armazenamento de avatar.');if(file_put_contents($path,$contents,LOCK_EX)===false)throw new \RuntimeException('Não foi possível salvar o avatar.');chmod($path,0600);return new StoredAvatar($key,$mimeType); }
    public function read(string $storageKey): ?string { $path=$this->path($storageKey);return is_file($path)?file_get_contents($path)?:null:null; }
    public function remove(string $storageKey): void { $path=$this->path($storageKey);if(is_file($path))unlink($path); }
    private function path(string $key): string { if($key===''||str_contains($key,'..')||str_starts_with($key,'/'))throw new \InvalidArgumentException('Chave de avatar inválida.');return $this->directory.'/'.$key; }
}
