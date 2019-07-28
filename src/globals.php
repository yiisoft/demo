<?php
function dd($variable) {
    \Yiisoft\VarDumper\VarDumper::dump($variable, 10, true);
}
