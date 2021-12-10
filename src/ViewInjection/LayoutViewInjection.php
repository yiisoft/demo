<?php

declare(strict_types=1);

namespace App\ViewInjection;

use App\Auth\Identity;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;

final class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    private CurrentUser $currentUser;

    public function __construct(CurrentUser $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function getLayoutParameters(): array
    {
        $identity = $this->currentUser->getIdentity();

        return [
            'brandLabel' => 'Yii Demo',
            'user' => $identity instanceof Identity ? $identity->getUser() : null,
        ];
    }
}
