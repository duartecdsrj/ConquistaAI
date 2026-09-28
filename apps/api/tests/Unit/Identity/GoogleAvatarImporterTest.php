<?php
declare(strict_types=1);
namespace Tests\Unit\Identity;
use App\Application\Identity\Port\{AvatarStorageInterface,StoredAvatar};use App\Infrastructure\Identity\GoogleAvatarImporter;use PHPUnit\Framework\TestCase;
final class GoogleAvatarImporterTest extends TestCase
{
 public function testItRejectsNonHttpsOrNonGoogleOriginsWithoutStoring():void{$storage=new AvatarImportStorage();$importer=new GoogleAvatarImporter($storage);self::assertNull($importer->import('user','http://lh3.googleusercontent.com/a.jpg'));self::assertNull($importer->import('user','https://example.test/a.jpg'));self::assertSame(0,$storage->stores);}
}
final class AvatarImportStorage implements AvatarStorageInterface {public int $stores=0;public function store(string $userId,string $contents,string $mimeType):StoredAvatar{$this->stores++;return new StoredAvatar('key','image/webp');}public function read(string $storageKey):?string{return null;}public function remove(string $storageKey):void{}}
