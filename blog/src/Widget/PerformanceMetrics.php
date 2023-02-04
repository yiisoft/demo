<?php

declare(strict_types=1);

namespace App\Widget;

use App\Timer;
use Yiisoft\Widget\Widget;

final class PerformanceMetrics extends Widget
{
    public function __construct(private Timer $timer)
    {
    }

    public function render(): string
    {
        $time = round($this->timer->get('overall'), 4);
        $memory = round(memory_get_peak_usage() / (1024 * 1024), 4);

        return "Time: $time s. Memory: $memory mb.";
    }
}
