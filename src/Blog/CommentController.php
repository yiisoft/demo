<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Comment\CommentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;

final class CommentController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/comments');
    }

    public function index(Request $request, CommentService $commentService, CurrentRoute $currentRoute): Response
    {
        $body = $request->getParsedBody();
        $paginator = $commentService->getFeedPaginator();

        $pageSize = (int) $currentRoute->getArgument(
            'pagesize',
            $body['pageSize'] ?? (string) PaginatorInterface::DEFAULT_PAGE_SIZE,
        );

        $paginator = $paginator->withPageSize($pageSize);

        if ($currentRoute->getArgument('page') !== null) {
            $paginator = $paginator
                ->withNextPageToken((string) $currentRoute->getArgument('page'))
                ->withPageSize($pageSize);
        }

        return $this->viewRenderer->render('index', ['paginator' => $paginator]);
    }
}
