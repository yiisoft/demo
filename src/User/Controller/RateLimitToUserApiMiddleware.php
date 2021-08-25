<?php

declare(strict_types=1);

namespace App\User\Controller;

use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Yii\RateLimiter\Counter;
use Yiisoft\Yii\RateLimiter\LimitRequestsMiddleware;
use Yiisoft\Yii\RateLimiter\Policy\LimitPerIp;
use Yiisoft\Yii\RateLimiter\Storage\StorageInterface;

class RateLimitToUserApiMiddleware implements MiddlewareInterface
{
    private StorageInterface $storage;

    private const LIMIT = 2;
    private const PERIOD_IN_SECOND = 5;
    private const TTR_COUNTER = 10;
    private const PREFIX_STORAGE = 'user-rate-limit-';

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $responseFactory = new ResponseFactory();
        $limitingPolicy = new LimitPerIp();

        $middleware = new LimitRequestsMiddleware($this->getCounter(), $responseFactory, $limitingPolicy);
        return $middleware->process($request, $handler);
    }

    private function getCounter(): Counter
    {
        return new Counter(
            $this->storage,
            self::LIMIT,
            self::PERIOD_IN_SECOND,
            self::TTR_COUNTER,
            self::PREFIX_STORAGE
        );
    }
}
