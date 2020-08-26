<?php

namespace App\Blog\Post;

use App\Blog\Entity\Post;
use App\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;

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

    public function add(ServerRequestInterface $request): Response {
        $body = $request->getParsedBody();
        $parameters = [
            'body' => $body,
        ];

        if ($request->getMethod() === Method::POST) {
            $sent = false;
            $error = '';

            try {
                foreach (['header', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $post = new Post($body['header'], $body['content']);
                //@TODO storage
                $sent = true;
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            $parameters['sent'] = $sent;
            $parameters['error'] = $error;
        }

        return $this->viewRenderer->withCsrf()->render('add', $parameters);
    }
}
