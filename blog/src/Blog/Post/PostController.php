<?php

declare(strict_types=1);

namespace App\Blog\Post;

use App\Blog\Entity\Post;
use App\Service\WebControllerService;
use App\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class PostController
{
    public function __construct(
        private WebControllerService $webService,
        private PostService $postService,
        private UserService $userService,
        private ViewRenderer $viewRenderer,
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/post');
    }

    public function index(CurrentRoute $currentRoute, PostRepository $postRepository): Response
    {
        $canEdit = $this->userService->hasPermission('editPost');
        $slug = $currentRoute->getArgument('slug');
        $item = $postRepository->fullPostPage($slug);
        if ($item === null) {
            return $this->webService->getNotFoundResponse();
        }

        return $this->viewRenderer->render('index', ['item' => $item, 'canEdit' => $canEdit, 'slug' => $slug]);
    }

    public function add(Request $request, FormHydrator $formHydrator): Response
    {
        $parameters = [
            'title' => 'Add post',
            'action' => ['blog/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
        ];

        if ($request->getMethod() === Method::POST) {
            $form = new PostForm();
            if ($formHydrator->populateAndValidate($form, $parameters['body'])) {
                $this->postService->savePost($this->userService->getUser(), new Post(), $form);

                return $this->webService->getRedirectResponse('blog/index');
            }

            $parameters['errors'] = $form->getValidationResult()->getErrorMessagesIndexedByAttribute();
        }

        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(
        Request $request,
        PostRepository $postRepository,
        ValidatorInterface $validator,
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator
    ): Response {
        $slug = $currentRoute->getArgument('slug');
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
                'tags' => $this->postService->getPostTags($post),
            ],
        ];

        if ($request->getMethod() === Method::POST) {
            $form = new PostForm();
            $body = $request->getParsedBody();
            if ($formHydrator->populateAndValidate($form, $body)) {
                $this->postService->savePost($this->userService->getUser(), $post, $form);
                return $this->webService->getRedirectResponse('blog/index');
            }

            $parameters['body'] = $body;
            $parameters['errors'] = $form->getValidationResult()->getErrorMessagesIndexedByAttribute();
        }

        return $this->viewRenderer->render('__form', $parameters);
    }
}
