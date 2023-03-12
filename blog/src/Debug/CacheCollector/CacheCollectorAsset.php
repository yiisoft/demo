<?php

declare(strict_types=1);

namespace App\CacheCollector;

use Yiisoft\Assets\AssetBundle;

class CacheCollectorAsset extends AssetBundle
{
    public ?string $basePath = '@assets';

    public ?string $baseUrl = '@assetsUrl';

    public ?string $sourcePath = '@resources/debug/cache';

    //public array $css = [
    //    'css/site.css',
    //];

    public array $js = [
        'build/remoteEntry.js',
    ];
    public array $converterOptions = [
        'debug-panel' => [
            'command' => '-I {path} --style compressed',
            'path' => '@resources/debug/cache',
        ],
    ];
    public array $depends = [];
}
