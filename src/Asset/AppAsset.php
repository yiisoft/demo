<?php

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class AppAsset extends AssetBundle
{
    public ?string $basePath = '@public';

    public ?string $baseUrl = '@web';

    public array $css = [
        [
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
            'integrity' => 'sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T',
            'crossorigin' => 'anonymous',
        ],
    ];

    public array $js = [
        'js/app.js',
    ];

    public array $depends = [];
}
