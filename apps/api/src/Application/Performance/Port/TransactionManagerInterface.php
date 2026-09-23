<?php
declare(strict_types=1);

namespace App\Application\Performance\Port;

interface TransactionManagerInterface
{
    public function transactional(callable $callback): mixed;
}
