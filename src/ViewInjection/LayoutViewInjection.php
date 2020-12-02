<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Csrf\CsrfToken;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;
use Yiisoft\User\User;

class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    private CsrfToken $csrf;
    private User $user;
    private UrlMatcherInterface $urlMatcher;

    public function __construct(
        CsrfToken $csrf,
        User $user,
        UrlMatcherInterface $urlMatcher
    ) {
        $this->csrf = $csrf;
        $this->user = $user;
        $this->urlMatcher = $urlMatcher;
    }

    public function getLayoutParameters(): array
    {
        return [
            'brandLabel' => 'Yii Demo',
            'csrf' => $this->csrf,
            'currentUrl' => (string)$this->urlMatcher->getCurrentUri(),
            'user' => $this->user->getIdentity(),
        ];
    }
}
