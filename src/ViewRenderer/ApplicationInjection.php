<?php

declare(strict_types=1);

namespace App\ViewRenderer;

use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\Web\User\User;

class ApplicationInjection extends AbstractInjection
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

    public function getLayoutParams(): array
    {
        return [
            'user' => $this->user->getIdentity(),
            'currentUrl' => (string)$this->urlMatcher->getLastMatchedRequest()->getUri(),
            'brandLabel' => 'Yii Demo',
        ];
    }

    public function getMetaTags(): array
    {
        return [
            [
                '__key' => 'generator',
                'name' => 'generator',
                'value' => 'Yii',
            ],
        ];
    }

    public function getLinkTags(): array
    {
        return [
            [
                '__key' => 'favicon',
                'name' => 'icon',
                'value' => 'favicon.ico',
            ],
        ];
    }
}
