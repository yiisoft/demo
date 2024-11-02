<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Comment\CommentRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class CommentController
{
    private const COMMENTS_FEED_PER_PAGE = 10;
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/comments');
    }

    public function index(
        Request $request,
        CommentRepository $repository,
        #[Body] ?array $body,
        #[Query('sort')] ?string $sortOrder = null,
        #[RouteArgument('page')] int $page = 1,
        #[RouteArgument('pagesize')] int $pageSize = null,
    ): Response {
        $dataReader = $repository
            ->findAll()
            ->withSort($repository->getSort()
                ->withOrderString($sortOrder ?? 'id'));

        if ($pageSize === null) {
            $pageSize = (int) ($body['pageSize'] ?? self::COMMENTS_FEED_PER_PAGE);
        }
        $paginator = (new OffsetPaginator($dataReader));
        $paginator = $paginator->withToken(PageToken::next((string) $page))->withPageSize($pageSize);


        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_comments', ['data' => $paginator]);
        }

        return $this->viewRenderer->render('index', ['data' => $paginator]);
    }

    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
}
