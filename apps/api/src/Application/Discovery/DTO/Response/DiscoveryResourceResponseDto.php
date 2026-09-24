<?php
declare(strict_types=1);namespace App\Application\Discovery\DTO\Response;final readonly class DiscoveryResourceResponseDto{public function __construct(public string $id,public string $type,public string $title,public string $sourceUrl,public string $provider,public string $queryText,public string $discoveredAt){}}
