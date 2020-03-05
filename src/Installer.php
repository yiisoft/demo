<?php

declare(strict_types=1);

namespace App;

use Composer\Script\Event;

final class Installer
{
    public static function postUpdate(Event $event = null): void
    {
        self::chmodRecursive('runtime', 0777);
    }

    private static function chmodRecursive(string $path, int $mode): void
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $item) {
            chmod($item->getPathname(), $mode);
            if ($item->isDir() && !$item->isDot()) {
                self::chmodRecursive($item->getPathname(), $mode);
            }
        }
    }
}
