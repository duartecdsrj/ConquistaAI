<?php
declare(strict_types=1);

use App\Application\Identity\DTO\Request\ProvisionUserRequestDto;
use App\Application\Identity\Service\ProvisionUserService;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\DoctrineTransactionManager;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineUserRepository;
use App\Infrastructure\Security\NativePasswordHasher;

require __DIR__ . '/../vendor/autoload.php';

[$script, $email, $name, $password, $roles] = array_pad($argv, 5, null);
if (!is_string($email) || !is_string($name) || !is_string($password) || !is_string($roles)) {
    fwrite(STDERR, "Uso: php bin/create-user.php <email> <nome> <senha-com-12-caracteres> <USER|ADMIN,USER>\n");
    exit(64);
}

$entityManager = DoctrineEntityManagerFactory::create();
$service = new ProvisionUserService(
    new DoctrineUserRepository($entityManager),
    new NativePasswordHasher(),
    new DoctrineTransactionManager($entityManager),
);

try {
    $user = $service->provision(new ProvisionUserRequestDto($email, $name, $password, explode(',', $roles)));
} catch (Throwable $exception) {
    fwrite(STDERR, "Nao foi possivel criar o usuario.\n");
    exit(1);
}

echo json_encode($user, JSON_THROW_ON_ERROR) . PHP_EOL;
