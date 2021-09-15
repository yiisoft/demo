<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Form\Widget\Field;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\CommonParametersInjectionInterface;

class ContentViewInjection implements CommonParametersInjectionInterface
{
    private UrlGeneratorInterface $url;
    private Field $field;

    public function __construct(
        UrlGeneratorInterface $url,
        Field $field
    ) {
        $this->url = $url;
        $this->field = $field;
    }

    public function getCommonParameters(): array
    {
        return [
            'field' => $this->field,
            'url' => $this->url,
        ];
    }
}
