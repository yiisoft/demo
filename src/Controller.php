<?php

namespace App;

use Generator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Factory\Factory;
use Yiisoft\Factory\FactoryInterface;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\User\User;

abstract class Controller implements ViewContextInterface
{
    protected FactoryInterface $factory;
    protected User $user;

    private Aliases $aliases;
    private WebView $view;
    private string $layout;

    public function __construct(
        Factory $factory,
        User $user,
        Aliases $aliases,
        WebView $view
    ) {
        $this->factory = $factory;
        $this->user = $user;
        $this->aliases = $aliases;
        $this->view = $view;
        $this->layout = $aliases->get('@views') . '/layout/main';
    }

    protected function render(string $view, array $parameters = []): ResponseInterface
    {
        $controller = $this;
        $renderer = function () use ($view, $parameters, $controller) {
            return $controller->renderContent($controller->view->render($view, $parameters, $controller));
        };

        return $this->factory->create(DeferredResponse::class, [$renderer]);
    }

    private function renderContent($content): string
    {
        $user = $this->user->getIdentity();

        $layout = $this->findLayoutFile($this->layout);
        if ($layout !== null) {
            return $this->view->renderFile(
                $layout,
                [
                    'content' => $content,
                    'user' => $user,
                ],
                $this
            );
        }

        return $content;
    }

    public function getViewPath(): string
    {
        return $this->aliases->get('@views') . '/' . $this->getId();
    }

    private function findLayoutFile(?string $file): ?string
    {
        if ($file === null) {
            return null;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }

        return $file . '.' . $this->view->getDefaultExtension();
    }

    abstract protected function getId(): string;
}
