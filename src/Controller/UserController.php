<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;

class UserController
{
    private const PAGINATION_INDEX = 5;

    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('user');
    }

    public function index(Request $request, UserRepository $userRepository): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);

        $dataReader = $userRepository->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->viewRenderer->render('index', ['paginator' => $paginator]);
    }

    public function profile(Request $request, UserRepository $userRepository, ResponseFactoryInterface $responseFactory): Response
    {
        $login = $request->getAttribute('login', null);
        $item = $userRepository->findByLogin($login);
        if ($item === null) {
            return $responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('profile', ['item' => $item]);
    }
}
