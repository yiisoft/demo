<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Form\Widget\Field;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\View\ContentParametersInjectionInterface;

class ContentViewInjection implements ContentParametersInjectionInterface
{
    private UrlMatcherInterface $urlMatcher;
    private UrlGeneratorInterface $url;
    private Field $field;

    public function __construct(
        UrlMatcherInterface $urlMatcher,
        UrlGeneratorInterface $url,
        Field $field
    ) {
        $this->urlMatcher = $urlMatcher;
        $this->url = $url;
        $this->field = $field;
    }

    public function getContentParameters(): array
    {
        return [
            'field' => $this->field,
            'url' => $this->url,
            'urlMatcher' => $this->urlMatcher,
        ];
    }
}
