<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
final readonly class StoredAvatar { public function __construct(public string $storageKey, public string $mimeType) {} }
interface AvatarStorageInterface
{
    public function store(string $userId, string $contents, string $mimeType): StoredAvatar;
    public function read(string $storageKey): ?string;
    public function remove(string $storageKey): void;
}
