<?php

declare(strict_types=1);

namespace App\Debug\CacheCollector;

use Yiisoft\Yii\Debug\Api\ModuleFederationAssetBundle;

final class CacheCollectorAsset extends ModuleFederationAssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@resources/debug/cache/build';

    public array $js = [
        'external.js.map',
        'external.js',
    ];

    public static function getModule(): string
    {
        return './CachePanel';
    }

    public static function getScope(): string
    {
        return 'remote';
    }
}
