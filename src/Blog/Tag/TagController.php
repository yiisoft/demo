<?php

namespace App\Blog\Tag;

use App\Blog\Post\PostRepository;
use App\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;

final class TagController extends Controller
{
    protected static ?string $name = 'blog/tag';
    private const POSTS_PER_PAGE = 10;

    public function index(Request $request, TagRepository $tagRepository, PostRepository $postRepository): Response
    {
        $label = $request->getAttribute('label', null);
        $pageNum = (int)$request->getAttribute('page', 1);
        $item = $tagRepository->findByLabel($label);

        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }
        // preloading of posts
        $paginator = (new OffsetPaginator($postRepository->findByTag($item->getId())))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'item' => $item,
            'paginator' => $paginator,
        ];
        return $this->render('index', $data);
    }
}
