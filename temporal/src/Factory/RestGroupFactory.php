<?php

declare(strict_types=1);

namespace App\Factory;

use ReflectionClass;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

final class RestGroupFactory
{
    private const ENTITY_PATTERN = '{id:\d+}';

    private const METHODS = [
        'get' => Method::GET,
        'list' => Method::GET,
        'post' => Method::POST,
        'put' => Method::PUT,
        'delete' => Method::DELETE,
        'patch' => Method::PATCH,
        'options' => Method::OPTIONS,
    ];

    public static function create(string $prefix, string $controller): Group
    {
        return Group::create($prefix)->routes(...self::createDefaultRoutes($controller));
    }

    private static function createDefaultRoutes(string $controller): array
    {
        $routes = [];
        $reflection = new ReflectionClass($controller);
        foreach (self::METHODS as $methodName => $httpMethod) {
            if ($reflection->hasMethod($methodName)) {
                $pattern = $methodName === 'list' ? '' : self::ENTITY_PATTERN;
                $routes[] = Route::methods([$httpMethod], $pattern)->action([$controller, $methodName]);
            }
        }

        return $routes;
    }
}
