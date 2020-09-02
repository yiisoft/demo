<?php

namespace App\Blog\Post;

use App\Blog\Entity\Post;
use App\Entity\User;
use App\ViewRenderer;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\Web\User\User as UserComponent;

final class PostController
{
    private ViewRenderer $viewRenderer;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ViewRenderer $viewRenderer, ResponseFactoryInterface $responseFactory)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/post');
        $this->responseFactory = $responseFactory;
    }

    public function index(Request $request, PostRepository $postRepository): Response
    {
        $slug = $request->getAttribute('slug', null);
        $item = $postRepository->fullPostPage($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('index', ['item' => $item]);
    }

    public function add(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator,
        UserComponent $userComponent
    ): Response {
        $body = $request->getParsedBody();
        $parameters = [
            'body' => $body,
            'action' => ['blog/add']
        ];

        if ($request->getMethod() === Method::POST) {
            $error = '';

            try {
                foreach (['header', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $post = new Post($body['header'], $body['content']);

                $userRepo = $orm->getRepository(User::class);
                $user = $userRepo->findByPK($userComponent->getId());

                $post->setUser($user);
                $post->setPublic(true);

                $transaction = new Transaction($orm);
                $transaction->persist($post);

                $transaction->run();

                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader(
                        'Location',
                        $urlGenerator->generate('blog/index')
                    );
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            $parameters['error'] = $error;
        }

        $parameters['title'] = 'Add post';
        return $this->viewRenderer->withCsrf()->render('__form', $parameters);
    }

    public function edit(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator,
        PostRepository $postRepository,
        AccessCheckerInterface $accessChecker,
        UserComponent $userComponent
    ): Response {
        $userId = $userComponent->getId();
        if (is_null($userId) || !$accessChecker->userHasPermission($userId, 'editPost')) {
            return $this->responseFactory->createResponse(403);
        }

        $slug = $request->getAttribute('slug', null);
        $post = $postRepository->fullPostPage($slug);
        if ($post === null) {
            return $this->responseFactory->createResponse(404);
        }

        $parameters = [];
        $parameters['action'] = ['blog/edit', ['slug' => $slug]];

        if ($request->getMethod() === Method::POST) {
            try {
                $body = $request->getParsedBody();
                $parameters['body'] = $body;

                foreach (['header', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $post->setTitle($body['header']);
                $post->setContent($body['content']);

                $transaction = new Transaction($orm);
                $transaction->persist($post);

                $transaction->run();

                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader(
                        'Location',
                        $urlGenerator->generate('blog/index')
                    );
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            $parameters['error'] = $error;
        } else {
            $parameters['body'] = [
                'header' => $post->getTitle(),
                'content' => $post->getContent(),
            ];
        }

        $parameters['title'] = 'Edit post';
        return $this->viewRenderer->withCsrf()->render('__form', $parameters);
    }
}
