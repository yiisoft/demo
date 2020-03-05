<?php

declare(strict_types=1);

namespace App;

use Composer\Script\Event;
use RecursiveIteratorIterator;

final class Installer
{
    public static function postUpdate(Event $event = null): void
    {
        self::chmodRecursive('runtime', 0777);
    }

    private static function chmodRecursive(string $path, int $mode): void
    {
        chmod($path, $mode);
        $iterator = new RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            $filename = $item->getFileName();
            if (!($filename === '.' || $filename === '..')) {
                chmod((string) $item, $mode);
            }
        }
    }
}
