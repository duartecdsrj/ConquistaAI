<?php
declare(strict_types=1);namespace App\Domain\Discovery\ValueObject;final readonly class DiscoveredResource{public function __construct(public string $type,public string $title,public string $sourceUrl){if(!in_array($type,['EXAM','ANSWER_KEY'],true))throw new \DomainException('Tipo de recurso invalido.');}}
