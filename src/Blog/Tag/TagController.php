<?php

namespace App\Blog\Tag;

use App\Blog\Post\PostRepository;
use App\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;

final class TagController
{
    private const POSTS_PER_PAGE = 10;
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/tag');
    }
    public function index(Request $request, TagRepository $tagRepository, PostRepository $postRepository, ResponseFactoryInterface $responseFactory): Response
    {
        $label = $request->getAttribute('label', null);
        $pageNum = (int)$request->getAttribute('page', 1);
        $item = $tagRepository->findByLabel($label);

        if ($item === null) {
            return $responseFactory->createResponse(404);
        }
        // preloading of posts
        $paginator = (new OffsetPaginator($postRepository->findByTag($item->getId())))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'item' => $item,
            'paginator' => $paginator,
        ];
        return $this->viewRenderer->render('index', $data);
    }
}
