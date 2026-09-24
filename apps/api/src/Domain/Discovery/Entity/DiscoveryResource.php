<?php
declare(strict_types=1);namespace App\Domain\Discovery\Entity;final readonly class DiscoveryResource{public function __construct(public string $id,public string $type,public string $title,public string $sourceUrl,public string $provider,public string $queryText,public \DateTimeImmutable $discoveredAt){}}
