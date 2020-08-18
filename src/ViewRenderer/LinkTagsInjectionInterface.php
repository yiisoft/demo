<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface LinkTagsInjectionInterface
{

    /**
     * Returns array of link tags for register via {@see \Yiisoft\View\WebView::registerLinkTag()}.
     * Use special key `__key` for set the key that identifies the link tag.
     *
     * For example:
     *
     * ```php
     * [
     *     [
     *         '__key' => 'favicon',
     *         'rel' => 'icon',
     *         'type' => 'image/png',
     *         'href' => '/myicon.png',
     *     ],
     *     ...
     * ]
     * ```
     *
     * @return array
     */
    public function getLinkTags(): array;
}
