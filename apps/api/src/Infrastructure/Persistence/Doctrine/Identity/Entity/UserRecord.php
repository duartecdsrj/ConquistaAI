<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class UserRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;

    #[ORM\Column(type: 'string', length: 190, unique: true)]
    public string $email;

    #[ORM\Column(type: 'string', length: 160)]
    public string $name;

    #[ORM\Column(name: 'password_hash', type: 'string', length: 255)]
    public string $passwordHash;

    #[ORM\Column(type: 'string', length: 16)]
    public string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $updatedAt;

    /** @var Collection<int, RoleRecord> */
    #[ORM\ManyToMany(targetEntity: RoleRecord::class)]
    #[ORM\JoinTable(name: 'user_roles')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'id')]
    public Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }
}
