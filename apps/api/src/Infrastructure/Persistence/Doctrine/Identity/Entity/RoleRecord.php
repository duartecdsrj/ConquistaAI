<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
class RoleRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(type: 'string', length: 32, unique: true)] public string $code;
}
