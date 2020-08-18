<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface MetaTagsInjectionInterface
{

    /**
     * Returns array of meta tags for register via {@see \Yiisoft\View\WebView::registerMetaTag()}.
     * Optionally, you may set the key that identifies the meta tag via `__key`.
     *
     * For example:
     *
     * ```php
     * [
     *     [
     *         '__key' => 'description',
     *          'name' => 'description',
     *          'content' => 'This website is about funny raccoons.'
     *     ],
     *     ...
     * ]
     * ```
     *
     * @return array
     */
    public function getMetaTags(): array;
}
