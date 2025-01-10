<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$io = new \Composer\IO\NullIO();
$composer = \Composer\Factory::create($io);

echo 'dumping...' . PHP_EOL;

\olvlvl\ComposerAttributeCollector\Plugin::dump(
    $composer,
    $io,
    __DIR__ . '/vendor/attributes.php',
);

echo 'done...' . PHP_EOL;
