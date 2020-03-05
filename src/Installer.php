<?php

namespace App;

use Composer\Script\Event;

class Installer
{
    public static function postInstall(Event $event): void
    {
        self::rchmod('runtime', 0777);
    }

    private static function rchmod(string $path, int $mode): void
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $item) {
            chmod($item->getPathname(), $mode);
            if ($item->isDir() && !$item->isDot()) {
                self::rchmod($item->getPathname(), $mode);
            }
        }
    }
}
