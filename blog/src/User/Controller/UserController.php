<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\User\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\RequestModel\Attribute\Body;
use Yiisoft\RequestModel\Attribute\Query;
use Yiisoft\RequestModel\Attribute\Route;
use Yiisoft\Yii\View\ViewRenderer;

final class UserController
{
    private const PAGINATION_INDEX = 5;

    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('user');
    }

    public function index(
        UserRepository $userRepository,
        #[Body] array $body,
        #[Query] array $sortOrder,
        #[Route('page')] int $page = 1,
        #[Route('pagesize')] int $pageSize = null,
    ): Response {
        $dataReader = $userRepository
            ->findAll()
            ->withSort(Sort::only(['id', 'login'])
            ->withOrderString($sortOrder['sort'] ?? 'id'));

        if ($pageSize === null) {
            $pageSize = (int) ($body['pageSize'] ?? OffSetPaginator::DEFAULT_PAGE_SIZE);
        }

        $paginator = (new OffsetPaginator($dataReader));
        $paginator = $paginator->withNextPageToken((string) $page)->withPageSize($pageSize);

        return $this->viewRenderer->render('index', ['paginator' => $paginator]);
    }

    public function profile(
        #[Route('login')] string $login,
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
