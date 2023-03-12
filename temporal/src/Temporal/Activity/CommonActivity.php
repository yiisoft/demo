<?php
declare(strict_types=1);

namespace App\Temporal\Activity;

#[\Temporal\Activity\ActivityInterface(prefix: "app.")]
class CommonActivity
{
    public function fast(string $name): string
    {
        return 'Hello, ' . $name;
    }

    public function slow(string $name): array
    {
        $start = microtime(true);
        sleep(random_int(1, 5));
        $end = microtime(true);

        return [
            'result' => 'Hello, ' . $name,
            'start' => $start,
            'end' => $end,
        ];
    }
}
