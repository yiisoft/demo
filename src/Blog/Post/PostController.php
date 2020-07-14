<?php

namespace App\Blog\Post;

use App\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostController extends Controller
{
    protected static ?string $name = 'blog/post';
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $slug = $request->getAttribute('slug', null);
        $item = $postRepository->fullPostPage($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->render('index', ['item' => $item]);
    }
}
