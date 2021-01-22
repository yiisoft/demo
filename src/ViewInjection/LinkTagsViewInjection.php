<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Yii\View\LinkTagsInjectionInterface;

class LinkTagsViewInjection implements LinkTagsInjectionInterface
{
    public function getLinkTags(): array
    {
        return [
            [
                '__key' => 'favicon',
                'rel' => 'icon',
                'href' => '/favicon.ico',
            ],
        ];
    }
}
