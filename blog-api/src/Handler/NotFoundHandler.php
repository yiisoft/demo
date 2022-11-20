<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\DataResponseFormatterInterface;
use Yiisoft\Http\Status;
use Yiisoft\Translator\TranslatorInterface;

final class NotFoundHandler implements RequestHandlerInterface
{
    public function __construct(
        private DataResponseFormatterInterface $formatter,
        private DataResponseFactoryInterface $dataResponseFactory,
        private TranslatorInterface $translator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->formatter->format(
            $this->dataResponseFactory->createResponse(
                $this->translator->translate('404.title'),
                Status::NOT_FOUND,
            )
        );
    }
}
