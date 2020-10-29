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
    IdentityRepositoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(\Cycle\ORM\ORMInterface::class)->getRepository(\App\Entity\User::class);
    }
];
