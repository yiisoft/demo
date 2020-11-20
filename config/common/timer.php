<?php

declare(strict_types=1);

use App\Timer;

$timer = new Timer();
$timer->start('overall');

return [
    Timer::class => $timer,
];
