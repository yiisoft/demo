<?php

declare(strict_types=1);

namespace App;

use function array_key_exists;
use function strpos;
use function explode;

/**
 * Parameters provides a way to get application parameters defined in config/params.php
 *
 * In order to use in a handler or any other place supporting auto-wired injection:
 *
 * ```php
 *
 * $params = [
 *      'admin' => [
 *          'email' => 'demo@example.com'
 *      ]
 * ];
 * ```
 *
 * ```php
 * public function actionIndex(Parameters $parameters)
 * {
 *     $adminEmail = $parameters->get('admin.email', 'admin@example.com'); // return demo@example.com or admin@example.com if search key not exists in parameters
 * }
 * ```
 */
class Parameters
{
    private array $parameters;
    private string $glue;

    public function __construct(array $data, string $glue = '.')
    {
        $this->parameters = $data;
        $this->glue = $glue;
    }

    public function get(string $key, $default = null)
    {
        if (strpos($key, $this->glue) !== false) {
            $keys = explode($this->glue, $key);
            if ($this->hasNesting($keys)) {
                return $this->getNesting($keys);
            }
        }

        return $this->has($key) ? $this->parameters[$key] : $default;
    }

    public function has(string $key): bool
    {
        if (strpos($key, $this->glue) !== false && $this->hasNesting(explode($this->glue, $key))) {
            return true;
        }

        return array_key_exists($key, $this->parameters);
    }

    public function getAll(): array
    {
        return $this->parameters;
    }

    private function hasNesting(array $keys): bool
    {
        $ref = $this->parameters;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $ref)) {
                return false;
            }
            $ref = $ref[$key];
        }
        
        return true;
    }

    private function getNesting(array $keys)
    {
        $data = $this->parameters;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                return null;
            }
            $data = $data[$key];
        }
        
        return $data;
    }
}
