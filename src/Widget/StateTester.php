<?php

declare(strict_types=1);

namespace App\Widget;

use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class StateTester extends Widget
{
    private WebView $webView;

    public function __construct(WebView $webView)
    {
        $this->webView = $webView;
    }

    protected function run(): string
    {
        $this->webView->registerMeta(['test' => 42]);
        return '';
    }
}
