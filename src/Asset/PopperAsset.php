<?php

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class PopperAsset extends AssetBundle
{
    public bool $cdn = true;
    public array $js = [
        [
            'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
            'integrity' => 'sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo',
            'crossorigin' => 'anonymous',
        ]
    ];
}
