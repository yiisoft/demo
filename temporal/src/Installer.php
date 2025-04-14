<?php

declare(strict_types=1);

namespace App;

use Composer\Script\Event;
use FilesystemIterator as FSIterator;
use RecursiveDirectoryIterator as DirIterator;
use RecursiveIteratorIterator as RIterator;

final class Installer
{
    /**
     * @psalm-suppress UndefinedClass
     */
    public static function postUpdate(Event $event = null): void
    {
        self::chmodRecursive('runtime', 0777);
        self::chmodRecursive('public/assets', 0777);
    }

    private static function chmodRecursive(string $path, int $mode): void
    {
        chmod($path, $mode);
        $iterator = new RIterator(
            new DirIterator($path, FSIterator::SKIP_DOTS | FSIterator::CURRENT_AS_PATHNAME),
            RIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            chmod($item, $mode);
        }
    }
}
