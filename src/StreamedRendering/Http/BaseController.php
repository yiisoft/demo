<?php

namespace App\StreamedRendering\Http;

use Generator;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Yiisoft\Injector\Injector;
use Yiisoft\Widget\WidgetFactory;

abstract class BaseController implements MiddlewareInterface, RequestHandlerInterface
{
    protected ResponseFactory $responseFactory;
    protected Container $container;
    protected Request $request;

    protected string $pageLayout = 'default';

    /**
     * baseController constructor.
     * @param Container $container
     * @param mixed[]   $options
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->responseFactory = $this->container->get(ResponseFactory::class);
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
        $args = $request->getAttributes();
        $method = strtoupper($request->getMethod());
        $page = $args['page'] ?? 'index';


        $action = $args['action'] ?? $request->getParsedBody()['action'] ?? $request->getQueryParams()['action'] ?? null;
        # find action
        if ($action !== null OR $method === 'POST') {
            $action = $action ?? $page;
            $method = 'action' . str_replace(' ', '', ucwords($action));
        } elseif ($method === 'GET') {
            $method = 'page' . str_replace(' ', '', ucwords($page));
        }
        if (!method_exists($this, $method)) {
            throw new HttpNotFoundException($request);
        }
        $response = (new Injector($this->container))->invoke([$this, $method], [$request]);

        return $response instanceof Response ? $response : $this->prepareResponse($response);
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
        $stream = new GeneratorStream($page);
        return $this->responseFactory->createResponse()->withBody($stream);
    }

    protected function handleError(Throwable $error): Response
    {
        throw $error;
    }
}
