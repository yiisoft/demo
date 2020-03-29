<?php

declare(strict_types=1);

namespace App\Stream\Data;

use Psr\Http\Message\MessageInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\WebView;

class WebViewConverter implements Converter, \Yiisoft\View\ViewContextInterface
{
    protected WebView $webView;
    protected Aliases $aliases;
    protected ?string $viewPath = null;

    public function __construct(WebView $webView, Aliases $aliases)
    {
        $this->webView = $webView;
        $this->aliases = $aliases;
    }
    public static function getFormat(): string
    {
        return 'text/html';
    }
    public function setHeaders(MessageInterface $message): MessageInterface
    {
        return $message->withHeader('Content-Type', self::getFormat());
    }
    public function convert($data, array $params = []) : string
    {
        if (!array_key_exists('view', $params)) {
            throw new \InvalidArgumentException('View should be defined in the params array');
        }
        if (array_key_exists('viewPath', $params)) {
            $this->viewPath =  $this->aliases->get($params['viewPath']);
        }
        $page = $this->webView->render(
            $this->aliases->get($params['view']),
            $data,
            $this->viewPath !== null ? $this : null
        );
        // render layout
        if (!array_key_exists('layout', $params)) {
            return $page;
        } else {
            return $this->webView->renderFile(
                $this->aliases->get($params['layout']),
                $this->getLayoutParams($page),
                $this->viewPath !== null ? $this : null
            );
        }
    }
    public function getViewPath(): string
    {
        return $this->viewPath;
    }
    protected function getLayoutParams(string $pageContent): array
    {
        return ['content' => $pageContent];
    }
}
