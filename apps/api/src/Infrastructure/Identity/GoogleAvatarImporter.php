<?php
declare(strict_types=1);
namespace App\Infrastructure\Identity;
use App\Application\Identity\Port\AvatarStorageInterface;use App\Application\Identity\Port\GoogleAvatarImporterInterface;use App\Domain\Identity\Entity\UserAvatar;use App\Domain\Identity\Enum\AvatarSource;
final class GoogleAvatarImporter implements GoogleAvatarImporterInterface
{
 public function __construct(private readonly AvatarStorageInterface $storage){}
 public function import(string $userId,?string $pictureUrl):?UserAvatar{try{if(!is_string($pictureUrl)||!$this->allowedUrl($pictureUrl))return null;$data=@file_get_contents($pictureUrl,false,stream_context_create(['http'=>['timeout'=>5,'follow_location'=>0,'ignore_errors'=>true]]));if(!is_string($data)||$data===''||strlen($data)>5*1024*1024)return null;$info=@getimagesizefromstring($data);if(!is_array($info)||$info[0]<32||$info[1]<32||$info[0]>4096||$info[1]>4096)return null;$image=@imagecreatefromstring($data);if($image===false)return null;ob_start();$ok=imagewebp($image,null,85);$webp=(string)ob_get_clean();imagedestroy($image);if(!$ok||$webp===''||strlen($webp)>5*1024*1024)return null;$stored=$this->storage->store($userId,$webp,'image/webp');return new UserAvatar($stored->storageKey,$stored->mimeType,AvatarSource::GOOGLE,new \DateTimeImmutable('now'));}catch(\Throwable){return null;}}
 private function allowedUrl(string $url):bool{$parts=parse_url($url);if(!is_array($parts)||($parts['scheme']??null)!=='https'||!is_string($parts['host']??null)||isset($parts['user'])||isset($parts['pass']))return false;$host=mb_strtolower($parts['host']);return $host==='googleusercontent.com'||str_ends_with($host,'.googleusercontent.com')||$host==='google.com'||str_ends_with($host,'.google.com');}
}
