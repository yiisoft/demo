<?php
namespace App\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * MiddlewareChain is a chaining of middlewares that implements MiddlewareInterface
 */
class MiddlewareChain implements MiddlewareInterface
{
    /**
     * @var MiddlewareInterface[] $middlewares
     */
    private $middlewares;

    /**
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(MiddlewareInterface ... $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handler = $handler;
        for ($i = count($this->middlewares) - 1; $i>0; $i--) {
            $handler = $this->wrap($this->middlewares[$i], $handler); 
        }

        return $this->middlewares[0]->process($request, $handler);
    }

    /**
     * Wraps handler by middlewares.
     * 
     * @param MiddlewareInterface $middleware
     * @param RequestHandlerInterface $handler
     * 
     * @return RequestHandlerInterface
     */
    private function wrap(MiddlewareInterface $middleware, RequestHandlerInterface $handler): RequestHandlerInterface
    {
        return new class($middleware, $handler) implements RequestHandlerInterface {
            /**
             * @var MiddlewareInterface
             */
            private $middleware;
            
            /**
             * @var RequestHandlerInterface
             */
            private $handler;

            /**
             * @param MiddlewareInterface $middleware
             * @param RequestHandlerInterface $handler
             */
            public function __construct($middleware, $handler)
            {
                $this->middleware = $middleware;
                $this->handler = $handler;
            }

            /**
             * {@inheritdoc}
             */
            public function handle(ServerRequestInterface $request): ResponseInterface {
                return $this->middleware->process($request, $this->handler);
            }
        };
    }
}
