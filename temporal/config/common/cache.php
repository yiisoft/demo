<?php

declare(strict_types=1);

use Yiisoft\Validator\IdMessageReader;

return [
    \Yiisoft\Translator\MessageReaderInterface::class =>IdMessageReader::class,
    \Yiisoft\Cache\CacheInterface::class => \Yiisoft\Cache\Cache::class,
    \Psr\SimpleCache\CacheInterface::class => \Yiisoft\Cache\File\FileCache::class,
];
