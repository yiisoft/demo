<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;

class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    private CurrentUser $currentUser;

    public function __construct(CurrentUser $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function getLayoutParameters(): array
    {
        return [
            'brandLabel' => 'Yii Demo',
            'user' => $this->currentUser->getIdentity(),
        ];
    }
}
