<?php

declare(strict_types=1);

use App\Env;

require_once __DIR__ . '/vendor/autoload.php';

$_SERVER['YII_ENV'] = $_ENV['YII_ENV'] = Env::get('YII_ENV');

$_SERVER['YII_DEBUG'] = $_ENV['YII_DEBUG'] = Env::getBoolean('YII_DEBUG');
