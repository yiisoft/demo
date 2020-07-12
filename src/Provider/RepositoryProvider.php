<?php

declare(strict_types=1);

namespace App\Provider;

use App\Blog\Comment\CommentRepository;
use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;

final class RepositoryProvider extends ServiceProvider
{
    private const REPOSITORIES = [
        User::class => UserRepository::class,
        Tag::class => TagRepository::class,
        Comment::class => CommentRepository::class,
        Post::class => PostRepository::class
    ];

    public function register(Container $container): void
    {
        $orm = $container->get(ORMInterface::class);
        foreach (self::REPOSITORIES as $entity => $repository) {
            $container->set($repository, $orm->getRepository($entity));
        }
    }
}
