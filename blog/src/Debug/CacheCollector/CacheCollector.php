<?php

declare(strict_types=1);

namespace App\Debug\CacheCollector;

use DateInterval;
use Yiisoft\Yii\Debug\Api\ModuleFederationProviderInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;

class CacheCollector implements ModuleFederationProviderInterface
{
    use CollectorTrait;

    public array $set = [];
    private array $get = [];

    public function collectGet(string $key): void
    {
        $this->get[$key] = [
            'key' => $key,
            'count' => ($this->get[$key]['count'] ?? 0) + 1,
        ];
    }

    public function collectSet(string $key, mixed $value, DateInterval|int|null $ttl): void
    {
        $this->set[$key] = [
            'key' => $key,
            'value' => $value,
            'ttl' => $ttl,
            'count' => ($this->set[$key]['count'] ?? 0) + 1,
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

    public function getIndexData(): array
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

    public static function getViewPath(): string
    {
        return '@views/debug/index';
    }

    public static function getAsset(): CacheCollectorAsset
    {
        return new CacheCollectorAsset();
    }
}
