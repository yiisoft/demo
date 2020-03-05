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
        chmod($path, $mode);
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }
            chmod($path, $mode);
            if ($item->isDir()) {
                self::chmodRecursive($item->getPathname(), $mode);
            }
        }
    }
}
