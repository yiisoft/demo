<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\Comment\CommentService;
use App\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CommentController extends Controller
{
    protected function getId(): string
    {
        return 'comment';
    }

    public function index(Request $request, CommentService $service): Response
    {
        $paginator = $service->getFeedPaginator();
        if ($request->getAttribute('next') !== null) {
            $paginator = $paginator->withNextPageToken((string)$request->getAttribute('next'));
        }

        if ($this->isAxaRequest($request)) {
            return $this->renderPartial('_comments', ['data' => $paginator]);
        }

        return $this->render('index', ['data' => $paginator]);
    }

    private function isAxaRequest(Request $request): bool
    {
        $params = $request->getServerParams();

        return !empty($params['HTTP_X_REQUESTED_WITH']) &&
            strtolower($params['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
