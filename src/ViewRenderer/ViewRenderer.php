<?php

declare(strict_types=1);

namespace App\ViewRenderer;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Strings\Inflector;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class ViewRenderer implements ViewContextInterface
{
    protected ?string $name = null;
    protected DataResponseFactoryInterface $responseFactory;

    private Aliases $aliases;
    private CsrfInjection $csrfInjection;
    private WebView $view;
    private string $layout;
    private ?string $viewBasePath;
    private ?string $viewPath = null;

    private array $injections;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        Aliases $aliases,
        CsrfInjection $csrfInjection,
        WebView $view,
        string $viewBasePath,
        string $layout,
        array $injections = []
    ) {
        $this->responseFactory = $responseFactory;
        $this->aliases = $aliases;
        $this->csrfInjection = $csrfInjection;
        $this->view = $view;
        $this->viewBasePath = $viewBasePath;
        $this->layout = $layout;
        $this->injections = $injections;
    }

    public function getViewPath(): string
    {
        if ($this->viewPath !== null) {
            return $this->viewPath;
        }

        return $this->aliases->get($this->viewBasePath) . '/' . $this->name;
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
        $new->name = static::getName($controller);

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

    public function withViewBasePath(string $viewBasePath): self
    {
        $new = clone $this;
        $new->viewBasePath = $viewBasePath;

        return $new;
    }

    public function withLayout(string $layout): self
    {
        $new = clone $this;
        $new->layout = $layout;

        return $new;
    }

    /**
     * @param InjectionInterface[] $injections
     * @return self
     */
    public function addInjections(array $injections): self
    {
        $new = clone $this;
        $new->injections = array_merge($this->injections, $injections);
        return $new;
    }

    public function addInjection(InjectionInterface $injection): self
    {
        return $this->addInjections([$injection]);
    }

    /**
     * @param InjectionInterface[] $injections
     * @return self
     */
    public function withInjections(array $injections): self
    {
        $new = clone $this;
        $new->injections = $injections;
        return $new;
    }

    private function renderProxy(string $view, array $parameters = []): string
    {
        $parameters = $this->inject($parameters, $this->injections, 'content');
        $content = $this->view->render($view, $parameters, $this);

        $layout = $this->findLayoutFile($this->layout);
        if ($layout === null) {
            return $content;
        }

        $layoutParameters = $this->inject(['content' => $content], $this->injections, 'layout');

        return $this->view->renderFile(
            $layout,
            $layoutParameters,
            $this,
        );
    }

    /**
     * @param array $parameters
     * @param InjectionInterface[] $injections
     * @param string $context
     * @return array
     */
    private function inject(array $parameters, array $injections, string $context): array
    {
        foreach ($injections as $injection) {
            switch ($context) {
                case 'content':
                    $parameters = array_merge($parameters, $injection->getContentParams());
                    foreach ($injection->getMetaTags() as $options) {
                        $key = ArrayHelper::remove($options, '__key');
                        $this->view->registerMetaTag($options, $key);
                    }
                    foreach ($injection->getLinkTags() as $options) {
                        $key = ArrayHelper::remove($options, '__key');
                        $this->view->registerLinkTag($options, $key);
                    }
                    break;

                case 'layout':
                    $parameters = array_merge($parameters, $injection->getLayoutParams());
                    break;
            }
        }
        return $parameters;
    }

    private function findLayoutFile(?string $file): ?string
    {
        if ($file === null) {
            return null;
        }

        $file = $this->aliases->get($file);

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
     * @param object $controller
     * @return string
     * @example App\Controller\FooBar\BazController -> foo-bar/baz
     * @example App\Controllers\FooBar\BazController -> foo-bar/baz
     * @example Path\To\File\BlogController -> blog
     * @see Inflector::camel2id()
     */
    private static function getName(object $controller): string
    {
        static $cache = [];

        $class = get_class($controller);
        if (isset($cache[$class])) {
            return $cache[$class];
        }

        $regexp = '/((?<=controller\\\|s\\\)(?:[\w\\\]+)|(?:[a-z]+))controller/iuU';
        if (!preg_match($regexp, $class, $m) || empty($m[1])) {
            throw new \RuntimeException('Cannot detect controller name');
        }

        $inflector = new Inflector();
        $name = str_replace('\\', '/', $m[1]);
        $name = $inflector->camel2id($name);

        $cache[$class] = $name;
        return $name;
    }
}
