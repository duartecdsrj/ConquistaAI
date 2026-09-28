<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
function reconcilesArenaVisibilityMigration(PDO $pdo): bool
{
    $column = $pdo->query("SHOW COLUMNS FROM arena_duels LIKE 'visibility'")->fetch(PDO::FETCH_ASSOC);
    $indexes = $pdo->query("SHOW INDEX FROM arena_duels WHERE Key_name = 'idx_arena_duels_visibility_status_created'")->fetchAll(PDO::FETCH_ASSOC);
    return is_array($column) && strtolower((string) ($column['Type'] ?? '')) === "enum('public','private')"
        && (string) ($column['Default'] ?? '') === 'PRIVATE'
        && array_column($indexes, 'Column_name') === ['visibility', 'status', 'created_at'];
}

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
        if ($version === '036_arena_visibility.sql' && reconcilesArenaVisibilityMigration($pdo)) {
            $pdo->prepare('INSERT INTO schema_migrations (version, applied_at) VALUES (?, UTC_TIMESTAMP())')->execute([$version]);
            fwrite(STDOUT, "Reconciled {$version}\n");
            continue;
        }
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        throw $exception;
    }
}
