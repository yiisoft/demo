<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Yii\View\MetaTagsInjectionInterface;

class MetaTagsViewInjection implements MetaTagsInjectionInterface
{
    public function getMetaTags(): array
    {
        return [
            [
                '__key' => 'generator',
                'name' => 'generator',
                'content' => 'Yii',
            ],
        ];
    }
}
