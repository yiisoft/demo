<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\CommonParametersInjectionInterface;

final class CommonViewInjection implements CommonParametersInjectionInterface
{
    private UrlGeneratorInterface $url;

    public function __construct(UrlGeneratorInterface $url)
    {
        $this->url = $url;
    }

    public function getCommonParameters(): array
    {
        return [
            'url' => $this->url,
        ];
    }
}
