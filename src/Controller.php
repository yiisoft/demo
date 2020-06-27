<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Strings\Inflector;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;
use Yiisoft\Yii\Web\User\User;

abstract class Controller implements ViewContextInterface
{
    protected static ?string $name = null;
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
        $contentRenderer = fn () => $this->renderProxy($view, $parameters);

        return $this->responseFactory->createResponse($contentRenderer);
    }

    protected function renderPartial(string $view, array $parameters = []): ResponseInterface
    {
        $content = $this->view->render($view, $parameters, $this);

        return $this->responseFactory->createResponse($content);
    }

    private function renderProxy(string $view, array $parameters = []): string
    {
        $content = $this->view->render($view, $parameters, $this);
        $user = $this->user->getIdentity();
        $layout = $this->findLayoutFile($this->layout);

        if ($layout === null) {
            return $content;
        }
        return $this->view->renderFile(
            $layout,
            [
                'content' => $content,
                'user' => $user,
            ],
            $this
        );
    }

    public function getViewPath(): string
    {
        return $this->aliases->get('@views') . '/' . static::getName();
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
     * Method returns classname without `controller` on the ending.
     * If namespace is not contain `controller` or `controllers`
     * then returns only classname without `controller` on the ending
     * else returns all subnamespaces from `controller` (or `controllers`) to the end
     *
     * @return string
     * @example App\Controller\FooBar\BazController -> foo-bar/baz
     * @example App\Controllers\FooBar\BazController -> foo-bar/baz
     * @example Path\To\File\BlogController -> blog
     * @see Inflector::camel2id()
     */
    protected static function getName(): string
    {
        if (static::$name !== null) {
            return static::$name;
        }

        $regexp = '/((?<=controller\\\|s\\\)(?:[\w\\\]+)|(?:[a-z]+))controller/iuU';
        if (!preg_match($regexp, static::class, $m) || empty($m[1])) {
            throw new \RuntimeException('Cannot detect controller name');
        }

        $inflector = new Inflector();
        $name = str_replace('\\', '/', $m[1]);

        return static::$name = $inflector->camel2id($name);
    }
}
