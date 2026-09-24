<?php
declare(strict_types=1);namespace App\Application\Discovery\Port;interface WebDiscoveryProviderInterface{/** @return list<\App\Domain\Discovery\ValueObject\DiscoveredResource> */public function search(string $query,string $type):array;public function name():string;}
