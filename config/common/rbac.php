<?php

declare(strict_types=1);

use App\Factory\MailerFactory;
use App\Timer;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponse;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\Php\Storage;
use Yiisoft\Rbac\RuleFactory\ClassNameRuleFactory;
use Yiisoft\Rbac\RuleFactoryInterface;
use Yiisoft\Rbac\StorageInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollection;

/** @var array $params */

return [
    StorageInterface::class => [
        '__class' => Storage::class,
        '__construct()' => [
            'directory' => $params['aliases']['@root'] . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'rbac'
        ]
    ],
    RuleFactoryInterface::class => ClassNameRuleFactory::class,
    AccessCheckerInterface::class => Manager::class,
];
