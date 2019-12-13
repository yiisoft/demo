<?php

namespace App\Asset;

use Yiisoft\Asset\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@public';
    public $baseUrl = '@web';
    public $css = [
        [
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
            'integrity' => 'sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T',
            'crossorigin' => 'anonymous',
        ],
    ];
    public $js = [
        'js/app.js',
    ];
    public $depends = [];
}
