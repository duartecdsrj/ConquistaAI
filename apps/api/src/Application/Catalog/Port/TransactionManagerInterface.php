<?php
declare(strict_types=1);
namespace App\Application\Catalog\Port;
interface TransactionManagerInterface { public function transactional(callable $callback): mixed; }
