<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

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
        private readonly DataResponseFormatterInterface $formatter,
        private readonly DataResponseFactoryInterface $dataResponseFactory,
        private readonly TranslatorInterface $translator,
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
