<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Strings\Inflector;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\User\User;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;

abstract class Controller implements ViewContextInterface
{
    protected static ?string $controllerName = null;
    protected DataResponseFactoryInterface $responseFactory;
    protected User $user;

    private Aliases $aliases;
    private WebView $view;
    private string $layout;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        User $user,
        Aliases $aliases,
        WebView $view
    ) {
        $this->responseFactory = $responseFactory;
        $this->user = $user;
        $this->aliases = $aliases;
        $this->view = $view;
        $this->layout = $aliases->get('@views') . '/layout/main';
    }

    protected function render(string $view, array $parameters = []): ResponseInterface
    {
        $controller = $this;
        $contentRenderer = static function () use ($view, $parameters, $controller) {
            return $controller->renderContent($controller->view->render($view, $parameters, $controller));
        };

        return $this->responseFactory->createResponse($contentRenderer);
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
        return $this->aliases->get('@views') . '/' . self::getName();
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

    /**
     * Returns the controller name. Name should be converted to "id" case.
     *
     * @return string
     * @example If class named MySiteController method will return my-site
     * @see Inflector::camel2id()
     */
    protected static function getName(): string
    {
        if (static::$controllerName !== null) {
            return static::$controllerName;
        }

        $name = preg_replace('/(?:.*\\\)([a-z]+)(controller)/iu', '$1', static::class);
        $inflector = new Inflector();

        return $inflector->camel2id($name);
    }
}
