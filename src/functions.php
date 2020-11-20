<?php

declare(strict_types=1);

if (!function_exists('dd')) {
    function dd(...$variables)
    {
        foreach ($variables as $variable) {
            \Yiisoft\VarDumper\VarDumper::dump($variable, 10, true);
        }
        die();
    }
}
