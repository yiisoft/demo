<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Comment\CommentService;
use App\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CommentController extends Controller
{
    protected function getId(): string
    {
        return 'blog/comments';
    }

    public function index(Request $request, CommentService $service): Response
    {
        $paginator = $service->getFeedPaginator();
        if ($request->getAttribute('next') !== null) {
            $paginator = $paginator->withNextPageToken((string)$request->getAttribute('next'));
        }

        if ($this->isAjaxRequest($request)) {
            return $this->renderPartial('_comments', ['data' => $paginator]);
        }

        return $this->render('index', ['data' => $paginator]);
    }

    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
}
