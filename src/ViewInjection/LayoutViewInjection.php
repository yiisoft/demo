<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\User\CurrentIdentity\CurrentIdentity;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;

class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    private CurrentIdentity $currentIdentity;

    public function __construct(CurrentIdentity $currentIdentity)
    {
        $this->currentIdentity = $currentIdentity;
    }

    public function getLayoutParameters(): array
    {
        return [
            'brandLabel' => 'Yii Demo',
            'user' => $this->currentIdentity->get(),
        ];
    }
}
