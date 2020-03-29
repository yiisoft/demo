<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Stream\Data\Converter;
use App\Stream\Data\PrintRConverter;
use App\Stream\DataStream;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RenderDataStream implements MiddlewareInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if (!$stream instanceof DataStream) {
            return $response;
        }
        $converterClass = $stream->getConverter();
        if ($converterClass === null) {
            # todo: get most relevant format from header
            $converterClass = PrintRConverter::class;
        }
        /** @var Converter $converter */
        $converter = $this->container->get($converterClass);
        $stream->render($converter);

        return $converter->setHeaders($response);
    }
}
