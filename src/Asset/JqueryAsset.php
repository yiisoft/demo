<?php

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class JqueryAsset extends AssetBundle
{
    public bool $cdn = true;
    public array $js = [
        [
            'https://code.jquery.com/jquery-3.4.1.slim.min.js',
            'integrity' => 'sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n',
            'crossorigin' => 'anonymous',
        ]
    ];
}
