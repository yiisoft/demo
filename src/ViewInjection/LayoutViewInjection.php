<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;
use Yiisoft\User\User;

class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    private User $user;
    private UrlMatcherInterface $urlMatcher;

    public function __construct(
        User $user,
        UrlMatcherInterface $urlMatcher
    ) {
        $this->user = $user;
        $this->urlMatcher = $urlMatcher;
    }

    public function getLayoutParameters(): array
    {
        return [
            'user' => $this->user->getIdentity(),
            'currentUrl' => (string)$this->urlMatcher->getCurrentUri(),
            'brandLabel' => 'Yii Demo',
        ];
    }
}
