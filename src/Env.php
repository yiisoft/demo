<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;
use Yiisoft\VarDumper\VarDumper;

final class Env
{
    private static ?array $values = null;

    private const FILE_NAME = '.env.php';

    public static function get(string $name, string $default = null): ?string
    {
        self::ensureValuesLoaded();

        return isset(self::$values[$name]) ? (string)self::$values[$name] : getenv($default) ?? $default;
    }

    public static function getBoolean(string $name, bool $default = false): bool
    {
        self::ensureValuesLoaded();

        return \filter_var(self::$values[$name] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function getInteger(string $name, int $default = 0): int
    {
        self::ensureValuesLoaded();

        return \filter_var(self::$values[$name] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function load(): void
    {
        $dotenv = Dotenv::createArrayBacked(dirname(__DIR__));
        $values = $dotenv->load();
        $values = VarDumper::create($values)->export();
        $content = <<<PHP
<?php

declare(strict_types=1);

return $values;

PHP;

        file_put_contents(dirname(__DIR__) . '/runtime/' . self::FILE_NAME, $content, LOCK_EX);
    }

    private static function ensureValuesLoaded(): void
    {
        if (self::$values === null) {
            $values = require dirname(__DIR__) . '/runtime/' . self::FILE_NAME;
            self::$values = $values + $_ENV + $_SERVER;
        }
    }
}
