<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Strings\Inflector;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;
use Yiisoft\Yii\Web\User\User;

final class ViewRenderer implements ViewContextInterface
{
    protected ?string $name = null;
    protected DataResponseFactoryInterface $responseFactory;
    protected User $user;

    private Aliases $aliases;
    private WebView $view;
    private string $layout;
    private ?string $viewPath = null;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        User $user,
        Aliases $aliases,
        WebView $view
    )
    {
        $this->responseFactory = $responseFactory;
        $this->user = $user;
        $this->aliases = $aliases;
        $this->view = $view;
        $this->layout = $aliases->get('@views') . '/layout/main';
    }

    public function getViewPath(): string
    {
        if ($this->viewPath !== null) {
            return $this->viewPath;
        }

        return $this->aliases->get('@views') . '/' . $this->name;
    }

    public function render(string $view, array $parameters = []): ResponseInterface
    {
        $contentRenderer = fn() => $this->renderProxy($view, $parameters);

        return $this->responseFactory->createResponse($contentRenderer);
    }

    public function renderPartial(string $view, array $parameters = []): ResponseInterface
    {
        $content = $this->view->render($view, $parameters, $this);

        return $this->responseFactory->createResponse($content);
    }

    public function withController(object $controller): self
    {
        $new = clone $this;
        $new->name = $this->getName($controller);

        return $new;
    }

    public function withControllerName(string $name): self
    {
        $new = clone $this;
        $new->name = $name;

        return $new;
    }

    public function withViewPath(string $viewPath): self
    {
        $new = clone $this;
        $new->viewPath = $viewPath;

        return $new;
    }

    public function withLayout(string $layout): self
    {
        $new = clone $this;
        $new->layout = $layout;

        return $new;
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
    private function getName(object $controller): string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        $regexp = '/((?<=controller\\\|s\\\)(?:[\w\\\]+)|(?:[a-z]+))controller/iuU';
        if (!preg_match($regexp, get_class($controller), $m) || empty($m[1])) {
            throw new \RuntimeException('Cannot detect controller name');
        }

        $inflector = new Inflector();
        $name = str_replace('\\', '/', $m[1]);

        return $inflector->camel2id($name);
    }
}
