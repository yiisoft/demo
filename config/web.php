<?php

use App\Blog\Comment\CommentRepository;
use App\Blog\Comment\CommentService;
use App\Blog\Entity\Comment;
use App\Contact\ContactMailer;
use App\Factory\MiddlewareDispatcherFactory;
use Cycle\ORM\ORMInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Yii\Web\Data\DataResponseFactory;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;
use Yiisoft\Yii\Web\Data\DataResponseFormatterInterface;
use Yiisoft\Yii\Web\Data\Formatter\HtmlDataResponseFormatter;
use Yiisoft\Yii\Web\MiddlewareDispatcher;
use Yiisoft\Yii\Web\Session\Session;
use Yiisoft\Yii\Web\Session\SessionInterface;

/**
 * @var array $params
 */

return [
    // PSR-17 factories:
    RequestFactoryInterface::class => Psr17Factory::class,
    ServerRequestFactoryInterface::class => Psr17Factory::class,
    ResponseFactoryInterface::class => Psr17Factory::class,
    StreamFactoryInterface::class => Psr17Factory::class,
    UriFactoryInterface::class => Psr17Factory::class,
    UploadedFileFactoryInterface::class => Psr17Factory::class,
    DataResponseFormatterInterface::class => HtmlDataResponseFormatter::class,
    DataResponseFactoryInterface::class => DataResponseFactory::class,

    MiddlewareDispatcher::class => new MiddlewareDispatcherFactory(),
    SessionInterface::class => [
        '__class' => Session::class,
        '__construct()' => [
            $params['session']['options'] ?? [],
            $params['session']['handler'] ?? null,
        ],
    ],

    // here you can configure custom prefix of the web path
    // \Yiisoft\Yii\Web\Middleware\SubFolder::class => [
    //     'prefix' => '',
    // ],

    // User:
    IdentityRepositoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(\Cycle\ORM\ORMInterface::class)->getRepository(\App\Entity\User::class);
    },

    // contact form mailer
    ContactMailer::class => static function (ContainerInterface $container) use ($params) {
        $mailer = $container->get(MailerInterface::class);
        return new ContactMailer($mailer, $params['supportEmail']);
    },

    CommentService::class => static function (ContainerInterface $container) {
        /**
         * @var CommentRepository $repository
         */
        $repository = $container->get(ORMInterface::class)->getRepository(Comment::class);

        return new CommentService($repository);
    },
];
