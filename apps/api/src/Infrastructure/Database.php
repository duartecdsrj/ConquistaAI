<?php
declare(strict_types=1);

namespace App\Infrastructure;

use PDO;

final class Database
{
    public static function pdo(): PDO
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', self::env('DB_HOST', 'mysql'), self::env('DB_PORT', '3306'), self::env('DB_NAME', 'concursos'));
        return new PDO($dsn, self::env('DB_USER', 'concursos'), self::env('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function env(string $key, ?string $default = null): string
    {
        return $_ENV[$key] ?? getenv($key) ?: $default ?? '';
    }
}
