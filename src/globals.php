<?php
function dd($variable) {
    \yii\helpers\VarDumper::dump($variable, 10, true);
}