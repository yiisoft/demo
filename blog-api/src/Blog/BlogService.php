<?php

declare(strict_types=1);

namespace App\Blog;

use App\Exception\NotFoundException;
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

    public function getPosts(int $page): PaginatorInterface
    {
        $dataReader = $this->postRepository->findAll();

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
