<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Infrastructure\Database;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

final class DoctrineEntityManagerFactory
{
    public static function create(): EntityManager
    {
        $configuration = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__],
            Database::env('APP_DEBUG', 'false') === 'true',
        );

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => Database::env('DB_HOST', 'mysql'),
            'port' => (int) Database::env('DB_PORT', '3306'),
            'dbname' => Database::env('DB_NAME', 'concursos'),
            'user' => Database::env('DB_USER', 'concursos'),
            'password' => Database::env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
        ], $configuration);

        return new EntityManager($connection, $configuration);
    }
}
