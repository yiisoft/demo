<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Yii\View\LayoutParametersInjectionInterface;
use Yiisoft\User\User;

class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    private User $user;

    public function __construct(
        User $user
    ) {
        $this->user = $user;
    }

    public function getLayoutParameters(): array
    {
        return [
            'brandLabel' => 'Yii Demo',
            'user' => $this->user->getIdentity(),
        ];
    }
}
