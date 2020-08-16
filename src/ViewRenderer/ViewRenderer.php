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
    private WebView $view;
    private string $layout;
    private ?string $viewBasePath;
    private ?string $viewPath = null;

    private array $injections;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        Aliases $aliases,
        WebView $view,
        string $viewBasePath,
        string $layout,
        array $injections = []
    ) {
        $this->responseFactory = $responseFactory;
        $this->aliases = $aliases;
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
        $contentRenderer = fn () => $this->renderProxy($view, $parameters);

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
     * @param ContentParamsInjectionInterface[]|LayoutParamsInjectionInterface[]|LinkTagsInjectionInterface[]|MetaTagsInjectionInterface[] $injections
     * @return self
     */
    public function addInjections(array $injections): self
    {
        $new = clone $this;
        $new->injections = array_merge($this->injections, $injections);
        return $new;
    }

    /**
     * @param ContentParamsInjectionInterface|LayoutParamsInjectionInterface|LinkTagsInjectionInterface|MetaTagsInjectionInterface $injection
     * @return self
     */
    public function addInjection($injection): self
    {
        return $this->addInjections([$injection]);
    }

    /**
     * @param ContentParamsInjectionInterface[]|LayoutParamsInjectionInterface[]|LinkTagsInjectionInterface[]|MetaTagsInjectionInterface[] $injections
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
        $this->injectMetaTags();
        $this->injectLinkTags();

        $parameters = $this->getContentParameters($parameters);
        $content = $this->view->render($view, $parameters, $this);

        $layout = $this->findLayoutFile($this->layout);
        if ($layout === null) {
            return $content;
        }

        $layoutParameters = $this->getLayoutParameters(['content' => $content]);

        return $this->view->renderFile(
            $layout,
            $layoutParameters,
            $this,
        );
    }

    private function injectMetaTags(): void
    {
        foreach ($this->injections as $injection) {
            if ($injection instanceof MetaTagsInjectionInterface) {
                foreach ($injection->getMetaTags() as $options) {
                    $key = ArrayHelper::remove($options, '__key');
                    $this->view->registerMetaTag($options, $key);
                }
            }
        }
    }

    private function injectLinkTags(): void
    {
        foreach ($this->injections as $injection) {
            if ($injection instanceof LinkTagsInjectionInterface) {
                foreach ($injection->getLinkTags() as $options) {
                    $key = ArrayHelper::remove($options, '__key');
                    $this->view->registerLinkTag($options, $key);
                }
            }
        }
    }

    private function getContentParameters(array $parameters): array
    {
        foreach ($this->injections as $injection) {
            if ($injection instanceof ContentParamsInjectionInterface) {
                $parameters = array_merge($parameters, $injection->getContentParams());
            }
        }
        return $parameters;
    }

    private function getLayoutParameters(array $parameters): array
    {
        foreach ($this->injections as $injection) {
            if ($injection instanceof LayoutParamsInjectionInterface) {
                $parameters = array_merge($parameters, $injection->getLayoutParams());
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
        return $cache[$class] = $inflector->camel2id($name);
    }
}
