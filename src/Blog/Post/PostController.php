<?php

namespace App\Blog\Post;

use App\Controller;
use App\Blog\Entity\Post;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostController extends Controller
{
    protected function getId(): string
    {
        return 'blog/post';
    }

    public function index(Request $request, ORMInterface $orm): Response
    {
        $postRepo = $orm->getRepository(Post::class);
        $slug = $request->getAttribute('slug', null);

        $item = $postRepo->fullPostPage($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->render('index', ['item' => $item]);
    }
}
