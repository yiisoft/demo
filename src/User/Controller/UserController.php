<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\User\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;

final class UserController
{
    private const PAGINATION_INDEX = 5;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('user');
    }

    public function index(
        CurrentRoute $currentRoute,
        ServerRequestInterface $request,
        UserRepository $userRepository
    ): Response {
        $page = (int)$currentRoute->getArgument('page', '1');
        $sortOrderString = $request->getQueryParams();

        $dataReader = $userRepository
            ->findAll()
            ->withSort(Sort::only(['id', 'login'])->withOrderString($sortOrderString['sort'] ?? ''));

        $paginator = (new OffsetPaginator($dataReader))->withPageSize(self::PAGINATION_INDEX);

        return $this->viewRenderer->render(
            'index',
            [
                'page' => $page,
                'paginator' => $paginator,
                'sortOrder' => $sortOrderString['sort'] ?? '',
            ]
        );
    }

    public function profile(
        CurrentRoute $currentRoute,
        ResponseFactoryInterface $responseFactory,
        UserRepository $userRepository
    ): Response {
        $login = $currentRoute->getArgument('login');
        $item = $userRepository->findByLogin($login);
        if ($item === null) {
            return $responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('profile', ['item' => $item]);
    }
}
