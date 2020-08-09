<?php

declare(strict_types=1);

namespace App\ViewRenderer;

use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Middleware\Csrf;

class CsrfInjection implements InjectionInterface
{
    public const DEFAULT_META_ATTRIBUTE = 'csrf';
    public const DEFAULT_PARAMETER = 'csrf';

    private UrlMatcherInterface $urlMatcher;
    private WebView $view;

    private string $requestAttribute = Csrf::REQUEST_NAME;
    private string $metaAttribute = self::DEFAULT_META_ATTRIBUTE;
    private string $parameter = self::DEFAULT_PARAMETER;

    public function __construct(
        UrlMatcherInterface $urlMatcher,
        WebView $view
    ) {
        $this->urlMatcher = $urlMatcher;
        $this->view = $view;
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

    public function getParams(): array
    {
        $csrfToken = $this->getCsrfToken();

        $this->view->registerMetaTag(
            [
                'name' => $this->metaAttribute,
                'content' => $this->csrfToken,
            ],
            'csrf_meta_tags'
        );

        return [$this->parameter => $csrfToken];
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
