<?php

declare(strict_types=1);

use App\Blog\Comment\CommentRepository;
use App\Blog\Comment\CommentService;
use App\Blog\Entity\Comment;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;

return [
    CommentService::class => static function (ContainerInterface $container) {
        /**
         * @var CommentRepository $repository
         */
        $repository = $container->get(ORMInterface::class)->getRepository(Comment::class);

        return new CommentService($repository);
    },
];
