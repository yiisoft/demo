<?php

declare(strict_types=1);

use App\Timer;

return [
    Timer::class => [
        '__class' => Timer::class,
        'start()' => ['overall']
    ]
];
