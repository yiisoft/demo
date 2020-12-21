<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Yii\Bootstrap5\Assets\BootstrapAsset;

class AppAsset extends AssetBundle
{
    public ?string $basePath = '@public';

    public ?string $baseUrl = '@baseUrl';

    public ?string $sourcePath = '@resources/asset';

    public array $css = [
        'css/site.css',
    ];

    public array $js = [
        'js/app.js',
    ];

    public array $depends = [
        BootstrapAsset::class,
    ];
}
