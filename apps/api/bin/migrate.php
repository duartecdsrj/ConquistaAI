<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$pdo = App\Infrastructure\Database::pdo();
$pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (version VARCHAR(255) PRIMARY KEY, applied_at DATETIME NOT NULL)');
foreach (glob(__DIR__ . '/../migrations/*.sql') ?: [] as $migration) {
    $version = basename($migration);
    $query = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE version = ?');
    $query->execute([$version]);
    if ($query->fetchColumn()) {
        continue;
    }
    $pdo->beginTransaction();
    try {
        $pdo->exec((string) file_get_contents($migration));
        $pdo->prepare('INSERT INTO schema_migrations (version, applied_at) VALUES (?, UTC_TIMESTAMP())')->execute([$version]);
        if ($pdo->inTransaction()) { $pdo->commit(); }
        fwrite(STDOUT, "Applied {$version}\n");
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $exception;
    }
}
