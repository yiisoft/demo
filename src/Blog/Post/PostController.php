<?php

declare(strict_types=1);

namespace App\Blog\Post;

use App\Blog\Entity\Post;
use App\Service\UserService;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;

final class PostController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private PostService $postService;
    private UserService $userService;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        PostService $postService,
        UserService $userService
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/post');
        $this->webService = $webService;
        $this->postService = $postService;
        $this->userService = $userService;
    }

    public function index(Request $request, PostRepository $postRepository): Response
    {
        $canEdit = $this->userService->hasPermission('editPost');
        $slug = $request->getAttribute('slug', null);
        $item = $postRepository->fullPostPage($slug);
        if ($item === null) {
            return $this->webService->getNotFoundResponse();
        }

        return $this->viewRenderer->render('index', ['item' => $item, 'canEdit' => $canEdit, 'slug' => $slug]);
    }

    public function add(Request $request, PostForm $form): Response
    {
        $parameters = [
            'title' => 'Add post',
            'action' => ['blog/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
        ];

        if ($request->getMethod() === Method::POST) {
            $form->load($parameters['body']);
            if ($form->validate()) {
                $this->postService->savePost($this->userService->getUser(), new Post(), $form);
                return $this->webService->getRedirectResponse('blog/index');
            }

            $parameters['errors'] = $form->firstErrors();
        }

        return $this->viewRenderer->withCsrf()->render('__form', $parameters);
    }

    public function edit(Request $request, PostForm $form, PostRepository $postRepository): Response
    {
        $slug = $request->getAttribute('slug', null);
        $post = $postRepository->fullPostPage($slug);
        if ($post === null) {
            return $this->webService->getNotFoundResponse();
        }

        $parameters = [
            'title' => 'Edit post',
            'action' => ['blog/edit', ['slug' => $slug]],
            'errors' => [],
            'body' => [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'tags' => $this->postService->getPostTags($post)
            ]
        ];

        if ($request->getMethod() === Method::POST) {
            $body = $request->getParsedBody();
            $form->load($body);
            if ($form->validate()) {
                $this->postService->savePost($this->userService->getUser(), $post, $form);
                return $this->webService->getRedirectResponse('blog/index');
            }

            $parameters['body'] = $body;
            $parameters['errors'] = $form->firstErrors();
        }

        return $this->viewRenderer->withCsrf()->render('__form', $parameters);
    }
}
