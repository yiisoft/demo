<?php

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class BootstrapAsset extends AssetBundle
{
    public bool $cdn = true;
    public array $css = [
        [
            'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',
            'integrity' => 'sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh',
            'crossorigin' => 'anonymous',
        ],
    ];

    public array $js = [
        [
            'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',
            'integrity' => 'sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6',
            'crossorigin' => 'anonymous',
        ]
    ];

    public array $depends = [
        JqueryAsset::class,
        PopperAsset::class,
    ];
}
