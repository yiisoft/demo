<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

final class Bootstrap5IconsAsset extends AssetBundle
{
    public bool $cdn = true;

    public array $css = [
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css',
    ];
}
