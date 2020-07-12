<?php

namespace App\Controller;

use App\Controller;
use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;

class UserController extends Controller
{
    private const PAGINATION_INDEX = 5;

    public function index(Request $request, UserRepository $userRepository): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);

        $dataReader = $userRepository->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->render('index', ['paginator' => $paginator]);
    }

    public function profile(Request $request, UserRepository $userRepository): Response
    {
        $login = $request->getAttribute('login', null);
        $item = $userRepository->findByLogin($login);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->render('profile', ['item' => $item]);
    }
}
