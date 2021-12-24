<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\User\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;

final class UserController
{
    private const PAGINATION_INDEX = 5;

    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('user');
    }

    public function index(UserRepository $userRepository, CurrentRoute $currentRoute): Response
    {
        $pageNum = (int)$currentRoute->getArgument('page', '1');

        $dataReader = $userRepository->findAll()->withSort(Sort::only(['login'])->withOrderString('login'));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->viewRenderer->render('index', ['paginator' => $paginator]);
    }

    public function profile(CurrentRoute $currentRoute, UserRepository $userRepository, ResponseFactoryInterface $responseFactory): Response
    {
        $login = $currentRoute->getArgument('login');
        $item = $userRepository->findByLogin($login);
        if ($item === null) {
            return $responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('profile', ['item' => $item]);
    }
}
