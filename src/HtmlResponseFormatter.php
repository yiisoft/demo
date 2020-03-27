<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Serializer\JsonSerializer;

final class HtmlResponseFormatter implements ResponseFormatterInterface
{
    /**
     * @var string the Content-Type header for the response
     */
    private string $contentType = 'text/html';

    /**
     * @var string the XML encoding. If not set, it will use the value of [[Response::charset]].
     */
    private string $encoding = 'UTF-8';

    public function format(DeferredResponse $response): ResponseInterface
    {
        $data = $response->getData();
        $response = $response->getResponse();
        $response->getBody()->write($data);

        return $response->withHeader('Content-Type', $this->contentType . '; charset=' . $this->encoding);
    }
}
