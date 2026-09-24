<?php
declare(strict_types=1);namespace App\Domain\Discovery\Repository;use App\Domain\Discovery\Entity\DiscoveryResource;interface DiscoveryResourceRepositoryInterface{public function save(DiscoveryResource $resource):void;/** @return list<DiscoveryResource> */public function list(?string $type=null):array;}
