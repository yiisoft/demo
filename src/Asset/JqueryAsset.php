<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class JqueryAsset extends AssetBundle
{
    public bool $cdn = true;
    public array $js = [
        [
            'https://code.jquery.com/jquery-3.4.1.min.js',
            'integrity' => 'sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=',
            'crossorigin' => 'anonymous',
        ]
    ];
}
