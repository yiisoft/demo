<?php

declare(strict_types=1);

namespace App\Application\Blog\Service;

use App\Application\Blog\Entity\Post\Post;
use App\Application\Blog\Entity\Post\PostRepository;
use App\Application\Exception\NotFoundException;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;

final class BlogService
{
    private const POSTS_PER_PAGE = 10;
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @psalm-return PaginatorInterface<array-key, Post>
     */
    public function getPosts(int $page): PaginatorInterface
    {
        $dataReader = $this->postRepository->findAll();

        /** @psalm-var PaginatorInterface<array-key, Post> */
        return (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($page);
    }

    /**
     * @param int $id
     *
     * @throws NotFoundException
     *
     * @return Post
     */
    public function getPost(int $id): Post
    {
        /**
         * @var Post|null $post
         */
        $post = $this->postRepository->findOne(['id' => $id]);
        if ($post === null) {
            throw new NotFoundException();
        }

        return $post;
    }
}
