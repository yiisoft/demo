<?php

declare(strict_types=1);

namespace App\Stream\Data;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Yii\Web\User\User;
use Yiisoft\View\WebView;

final class MyWebViewConverter extends WebViewConverter
{
    protected ?string $viewPath = '@view';
    private User $identity;

    public function __construct(WebView $webView, Aliases $aliases, User $identity)
    {
        parent::__construct($webView, $aliases);
        $this->identity = $identity;
    }
    protected function getLayoutParams(string $pageContent): array
    {
        return [
            'content' => $pageContent,
            'user' => $this->identity->getIdentity(),
        ];
    }
}
