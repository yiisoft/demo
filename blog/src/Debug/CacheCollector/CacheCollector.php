<?php

declare(strict_types=1);

namespace App\Debug\CacheCollector;

use DateInterval;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;

class CacheCollector implements CollectorInterface, SummaryCollectorInterface
{
    use CollectorTrait;

    public array $set = [];
    private array $get = [];

    public function collectGet(string $key): void
    {
        $this->get[$key] = $key;
    }

    public function collectSet(string $key, mixed $value, DateInterval|int|null $ttl)
    {
        $this->set[$key] = [
            'key' => $key,
            'value' => $value,
            'ttl' => $ttl,
        ];
    }

    public function getCollected(): array
    {
        return [
            'cache' => [
                'get' => array_values($this->get),
                'set' => array_values($this->set),
            ],
        ];
    }

    public function getSummary(): array
    {
        return [
            'cache' => [
                'get' => [
                    'total' => array_values($this->get),
                ],
                'set' => [
                    'total' => array_values($this->set),
                ],
            ],
        ];
    }

    public static function getView(): string
    {
        return '@views/debug/index';
    }
}
