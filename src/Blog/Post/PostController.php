<?php

namespace App\Blog\Post;

use App\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/post');
    }

    public function index(Request $request, PostRepository $postRepository, ResponseFactoryInterface $responseFactory): Response
    {
        $slug = $request->getAttribute('slug', null);
        $item = $postRepository->fullPostPage($slug);
        if ($item === null) {
            return $responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('index', ['item' => $item]);
    }
}
