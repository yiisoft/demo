<?php

declare(strict_types=1);

if (!function_exists('d')) {
    function d(...$variables)
    {
        foreach ($variables as $variable) {
            \Yiisoft\VarDumper\VarDumper::dump($variable, 10, PHP_SAPI !== 'cli');
        }
    }
}

if (!function_exists('dd')) {
    function dd(...$variables)
    {
        foreach ($variables as $variable) {
            \Yiisoft\VarDumper\VarDumper::dump($variable, 10, PHP_SAPI !== 'cli');
        }
        die();
    }
}
