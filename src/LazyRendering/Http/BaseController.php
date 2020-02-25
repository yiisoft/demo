<?php

namespace App\LazyRendering\Http;

use Generator;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Yiisoft\Http\Method;
use Yiisoft\Injector\Injector;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;

abstract class BaseController implements MiddlewareInterface
{
    protected ResponseFactory $responseFactory;
    protected Request $request;
    protected UrlGeneratorInterface $urlGenerator;
    /** @var null|mixed Layout definition with method render() */
    protected $pageLayout = null;
    protected StreamFactoryInterface $streamFactory;

    private Container $container;

    /**
     * baseController constructor.
     * @param Container $container
     * @param mixed[] $options
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->responseFactory = $this->container->get(ResponseFactory::class);
        $this->urlGenerator = $this->container->get(UrlGeneratorInterface::class);
        $this->streamFactory = $this->container->get(StreamFactoryInterface::class);
        WidgetFactory::initialize($container);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Throwable
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->flushAllOutputBuffers();
        return $this->handle($request) ?? $handler->handle($request);
    }

    public function handle(Request $request): ?Response
    {
        $this->request = $request;
        $actionMethod = $this->getActionMethod($request);
        if ($actionMethod === null || !method_exists($this, $actionMethod)) {
            return null;
        }
        $data = (new Injector($this->container))->invoke([$this, $actionMethod]);

        $response = $data instanceof Response ? $data : $this->prepareResponse($data);

        // Force Buffering (classic mode)
        if (($this->request->getQueryParams()['forceBuffering'] ?? 0) === '1') {
            // Buffering from generator
            $content = $response->getBody()->getContents();
            $stream = $this->streamFactory->createStream($content);
            return $response->withBody($stream);
        }

        if (($this->request->getQueryParams()['forceBuffering'] ?? 0) === '2') {
            $stream = $response->getBody();
            if (!$stream instanceof GeneratorStream) {
                throw new \Exception('Combined mode not supported');
            }
            $stream->setReadMode(GeneratorStream::READ_MODE_FIRST_YIELD);
        }
        return $response;
    }

    protected function prepareResponse(iterable $page): Response
    {
        if (!$page instanceof Generator) {
            $page = (static function (iterable $iterable) {
                yield from $iterable;
            })($page);
        }
        // Add layout rendering
        if ($this->pageLayout !== null) {
            if (is_string($this->pageLayout)) {
                $this->pageLayout = $this->container->get($this->pageLayout);
            } elseif (!is_object($this->pageLayout) || !method_exists($this->pageLayout, 'render')) {
                throw new \RuntimeException('Bad Layout definition');
            }
            $page = (new Injector($this->container))->invoke([$this->pageLayout, 'render'], [$page, $this->request]);
        }
        $stream = new GeneratorStream($page);
        return $this->responseFactory->createResponse()->withBody($stream);
    }

    private function flushAllOutputBuffers(): void
    {
        for ($bufferingLevel = ob_get_level(), $i = 0; $i < $bufferingLevel; ++$i) {
            ob_end_flush();
        }
    }

    private function getActionMethod(Request $request): ?string
    {
        $method = strtoupper($request->getMethod());
        $page = $request->getAttribute('page', 'index');
        $action =
            $request->getAttribute('action') ??
            $request->getParsedBody()['action'] ??
            $request->getQueryParams()['action'] ??
            null;

        if ($action !== null || $method === Method::POST) {
            $action = $action ?? $page;
            return 'action' . str_replace(' ', '', ucwords($action));
        }

        if ($method === Method::GET) {
            return 'page' . str_replace(' ', '', ucwords($page));
        }

        return null;
    }
}
