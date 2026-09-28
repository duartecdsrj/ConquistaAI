<?php declare(strict_types=1); namespace App\Application\Review\Port; interface TransactionManagerInterface { public function transactional(callable $callback):mixed; }
