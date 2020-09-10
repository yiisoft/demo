<?php

declare(strict_types=1);

namespace App\Blog\Post;

use App\ViewRenderer\ViewRenderer;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Entity\User;
use App\ViewRenderer;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\Web\User\User as UserComponent;

final class PostController
{
    private ViewRenderer $viewRenderer;
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;
    private UserComponent $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger,
        UserComponent $userService
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/post');
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->userService = $userService;
    }

    public function index(Request $request, PostRepository $postRepository, AccessCheckerInterface $accessChecker): Response
    {
        $userId = $this->userService->getId();
        $canEdit = !is_null($userId) && $accessChecker->userHasPermission($userId, 'editPost');

        $slug = $request->getAttribute('slug', null);
        $item = $postRepository->fullPostPage($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('index', ['item' => $item, 'canEdit' => $canEdit, 'slug' => $slug]);
    }

    public function add(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        if ($this->userService->isGuest()) {
            return $this->responseFactory->createResponse(403);
        }

        $body = $request->getParsedBody();
        $parameters = [
            'body' => $body,
            'action' => ['blog/add']
        ];

        if ($request->getMethod() === Method::POST) {
            $error = '';

            try {
                foreach (['title', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $post = new Post($body['title'], $body['content']);

                $userRepo = $orm->getRepository(User::class);
                $user = $userRepo->findByPK($this->userService->getId());

                $post->setUser($user);
                $post->setPublic(true);

                $tagRepository = $orm->getRepository(Tag::class);
                foreach ($body['tags'] ?? [] as $tag) {
                    $tagEntity = $tagRepository->getOrCreate($tag);
                    $post->addTag($tagEntity);
                }

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
                $this->logger->error($e);
                $error = $e->getMessage();
            }

            $parameters['error'] = $error;
        }

        $parameters['title'] = 'Add post';
        $parameters['tags'] = [];
        return $this->viewRenderer->withCsrf()->render('__form', $parameters);
    }

    public function edit(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator,
        PostRepository $postRepository,
        AccessCheckerInterface $accessChecker
    ): Response {
        $userId = $this->userService->getId();
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
            $error = '';

            try {
                $body = $request->getParsedBody();
                $parameters['body'] = $body;

                foreach (['title', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $post->setTitle($body['title']);
                $post->setContent($body['content']);

                $tagRepository = $orm->getRepository(Tag::class);
                $post->resetTags();
                foreach ($body['tags'] ?? [] as $tag) {
                    $tagEntity = $tagRepository->getOrCreate($tag);
                    $post->addTag($tagEntity);
                }

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
                $this->logger->error($e);
                $error = $e->getMessage();
            }

            $parameters['error'] = $error;
        } else {
            $parameters['body'] = [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
            ];
        }

        $parameters['title'] = 'Edit post';
        $parameters['tags'] = array_map(function (Tag $tag) {
            return $tag->getLabel();
        }, $post->getTags());
        return $this->viewRenderer->withCsrf()->render('__form', $parameters);
    }
}
