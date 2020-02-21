<?php

namespace App;

final class Timer
{
    private static array $timers = [];

    public static function start(string $name): void
    {
        static::$timers[$name] = microtime(true);
    }

    public static function get(string $name): float
    {
        if (!array_key_exists($name, static::$timers)) {
            throw new \InvalidArgumentException("There is no \"$name\" timer started");
        }

        return microtime(true) - static::$timers[$name];
    }
}
