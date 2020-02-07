<?php

/* @var \Psr\Container\ContainerInterface $container */

if (!function_exists('dd')) {
    function dd(...$variables)
    {
        foreach ($variables as $variable) {
            \Yiisoft\VarDumper\VarDumper::dump($variable, 10, true);
        }
        die();
    }
}
