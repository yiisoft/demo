<?php

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class AppAsset extends AssetBundle
{
    public ?string $basePath = '@public';

    public ?string $baseUrl = '@web';

    public array $css = [];

    public array $js = [
        'js/app.js',
    ];

    public array $depends = [
        BootstrapAsset::class
    ];
}
