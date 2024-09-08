<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\User\UserRepository;
use Yiisoft\Data\Paginator\PageToken;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class UserController
{
    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('user');
    }

    public function index(
        UserRepository $userRepository,
        #[Body] ?array $body,
        #[Query('sort')] ?string $sortOrder = null,
        #[RouteArgument('page')] int $page = 1,
        #[RouteArgument('pagesize')] int $pageSize = null,
    ): Response {
        $dataReader = $userRepository
            ->findAll()
            ->withSort(Sort::only(['id', 'login'])
            ->withOrderString($sortOrder ?? 'id'));

        if ($pageSize === null) {
            $pageSize = (int) ($body['pageSize'] ?? OffSetPaginator::DEFAULT_PAGE_SIZE);
        }

        $paginator = (new OffsetPaginator($dataReader));
        $paginator = $paginator->withToken(PageToken::next((string) $page))->withPageSize($pageSize);

        return $this->viewRenderer->render('index', ['paginator' => $paginator]);
    }

    public function profile(
        #[RouteArgument('login')] string $login,
        ResponseFactoryInterface $responseFactory,
        UserRepository $userRepository
    ): Response {
        $item = $userRepository->findByLogin($login);

        if ($item === null) {
            return $responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('profile', ['item' => $item]);
    }
}
