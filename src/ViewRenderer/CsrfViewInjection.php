<?php

declare(strict_types=1);

namespace App\ViewRenderer;

use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\Web\Middleware\Csrf;

class CsrfViewInjection implements
    ContentParamsInjectionInterface,
    LayoutParamsInjectionInterface,
    MetaTagsInjectionInterface
{
    public const DEFAULT_META_ATTRIBUTE = 'csrf';
    public const DEFAULT_PARAMETER = 'csrf';

    private UrlMatcherInterface $urlMatcher;

    private string $requestAttribute = Csrf::REQUEST_NAME;
    private string $metaAttribute = self::DEFAULT_META_ATTRIBUTE;
    private string $parameter = self::DEFAULT_PARAMETER;

    public function __construct(UrlMatcherInterface $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
    }

    public function withRequestAttribute(string $requestAttribute): self
    {
        $clone = clone $this;
        $clone->requestAttribute = $requestAttribute;
        return $clone;
    }

    public function withParameter(string $parameter): self
    {
        $clone = clone $this;
        $clone->parameter = $parameter;
        return $clone;
    }

    public function withMetaAttribute(string $metaAttribute): self
    {
        $clone = clone $this;
        $clone->metaAttribute = $metaAttribute;
        return $clone;
    }

    public function getContentParams(): array
    {
        return [$this->parameter => $this->getCsrfToken()];
    }

    public function getLayoutParams(): array
    {
        return [$this->parameter => $this->getCsrfToken()];
    }

    public function getMetaTags(): array
    {
        return [
            [
                '__key' => 'csrf_meta_tags',
                'name' => $this->metaAttribute,
                'content' => $this->getCsrfToken(),
            ]
        ];
    }

    private ?string $csrfToken = null;

    private function getCsrfToken(): string
    {
        if ($this->csrfToken === null) {
            $this->csrfToken = $this->urlMatcher->getLastMatchedRequest()->getAttribute($this->requestAttribute);
        }
        return $this->csrfToken;
    }
}
