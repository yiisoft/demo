<?php

declare(strict_types=1);

use Spatie\Watcher\Watch;

require_once __DIR__ . '/vendor/autoload.php';

echo 'Run watcher-build...' . PHP_EOL;

`php watcher-build.php`;

echo 'Ready for listening changes...' . PHP_EOL;

Watch::paths(__DIR__ . '/src')
    ->onAnyChange(function (string $type, string $path) {
        echo sprintf('File changed: "%s".', $path) . PHP_EOL;
        echo 'Run watcher-build...' . PHP_EOL;
        `php watcher-build.php`;
        echo 'Dumped...' . PHP_EOL;
    })
    ->start();
