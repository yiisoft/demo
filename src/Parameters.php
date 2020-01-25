<?php

namespace App;

/**
 * Parameters provides a way to get application parameters defined in config/params.php
 *
 * In order to use in a handler or any other place supporting auto-wired injection:
 *
 * ```php
 * public function actionIndex(Parameters $parameters)
 * {
 *     $adminEmail = $parameters->get('admin.email', 'admin@example.com');
 * }
 * ```
 */
class Parameters
{
    private array $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function get(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }
}
