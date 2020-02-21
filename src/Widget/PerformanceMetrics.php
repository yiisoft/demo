<?php


namespace App\Widget;

use App\Timer;
use Yiisoft\Widget\Widget;

class PerformanceMetrics extends Widget
{
    private Timer $timer;

    protected function run(): string
    {
        return sprintf(
            "Time: %.6f s. Memory: %.3f mb.",
            Timer::get('overall'),
            memory_get_peak_usage() / (1024 * 1024)
        );
    }
}
