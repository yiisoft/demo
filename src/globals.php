<?php
/* @var \Psr\Container\ContainerInterface $container */

if (!function_exists('dd')) {
   function dd(...$variables) {
       foreach ($variables as $dumpVariable) {
           \Yiisoft\VarDumper\VarDumper::dump($dumpVariable, 10, true);
       }

       die();
   }
}
