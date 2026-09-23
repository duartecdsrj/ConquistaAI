<?php
declare(strict_types=1);

namespace App\Application\Identity\Service;

use App\Application\Identity\DTO\Request\ProvisionUserRequestDto;
use App\Application\Identity\DTO\Response\ProvisionUserResponseDto;
use App\Application\Identity\Port\PasswordHasherInterface;
use App\Application\Identity\Port\TransactionManagerInterface;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Exception\UserAlreadyExistsException;
use App\Domain\Identity\Repository\UserRepositoryInterface;

final class ProvisionUserService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function provision(ProvisionUserRequestDto $request): ProvisionUserResponseDto
    {
        $email = mb_strtolower(trim($request->email));
        $name = trim($request->name);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || $name === '' || strlen($request->password) < 12) {
            throw new \InvalidArgumentException('Dados de usuario invalidos.');
        }

        $roles = array_values(array_unique(array_map(static fn (string $role): string => strtoupper(trim($role)), $request->roles)));
        if ($roles === [] || array_diff($roles, ['ADMIN', 'USER']) !== []) {
            throw new \InvalidArgumentException('Papeis invalidos.');
        }

        return $this->transactions->transactional(function () use ($email, $name, $request, $roles): ProvisionUserResponseDto {
            if ($this->users->findByEmail($email) !== null) {
                throw new UserAlreadyExistsException();
            }

            $user = new User(
                $this->uuid(),
                $email,
                $name,
                $this->passwordHasher->hash($request->password),
                'ACTIVE',
                $roles,
            );
            $this->users->save($user);

            return new ProvisionUserResponseDto($user->id, $user->email, $user->name, $user->roles);
        });
    }

    private function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
