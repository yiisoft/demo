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
use Yiisoft\Injector\Injector;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;

abstract class BaseController implements MiddlewareInterface, RequestHandlerInterface
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
     * @param mixed[]   $options
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
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws HttpException
     * @throws Throwable
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        # disable output buffering
        for ($j = ob_get_level(), $i = 0; $i < $j; ++$i) {
            ob_end_flush();
        }

        try {
            return $this->handle($request);
        } catch (HttpNotFoundException $e) {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->handleError($e);
        }
    }

    public function handle(Request $request): Response
    {
        $this->request = $request;
        $args = $request->getAttributes();
        $method = strtoupper($request->getMethod());
        $page = $args['page'] ?? 'index';


        $action = $args['action'] ?? $request->getParsedBody()['action'] ?? $request->getQueryParams()['action'] ?? null;
        # find action
        if ($action !== null or $method === 'POST') {
            $action = $action ?? $page;
            $method = 'action' . str_replace(' ', '', ucwords($action));
        } elseif ($method === 'GET') {
            $method = 'page' . str_replace(' ', '', ucwords($page));
        }
        if (!method_exists($this, $method)) {
            throw new HttpNotFoundException($request);
        }
        $data = (new Injector($this->container))->invoke([$this, $method]);

        $response = $data instanceof Response ? $data : $this->prepareResponse($data);

        // Force Buffering (classic mode)
        if (($this->request->getQueryParams()['forceBuffering'] ?? 0) === '1') {
            $content = $response->getBody()->getContents();
            $stream = $this->streamFactory->createStream($content);
            return $response->withBody($stream);
        } elseif (($this->request->getQueryParams()['forceBuffering'] ?? 0) === '2') {
            $stream = $response->getBody();
            if (!$stream instanceof GeneratorStream) {
                throw new \Exception('Combined mode not supported');
            }
            $stream->setReadMode(GeneratorStream::READ_MODE_FIRST_YIELD);
        }
        return $response;
    }

    public function isAjax(): bool
    {
        $headers = $this->request->getHeader('x-requested-with');
        return in_array('XMLHttpRequest', $headers);
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

    protected function handleError(Throwable $error): Response
    {
        throw $error;
    }
}
