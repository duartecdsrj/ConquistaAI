<?php
declare(strict_types=1);

use App\Infrastructure\ContainerFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = ContainerFactory::create();
$app->run();
