<?php

namespace App\Controller;

use App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Router\UrlGeneratorInterface;

class UserController extends Controller
{
    private const PAGINATION_INDEX = 5;

    protected function getId(): string
    {
        return 'user';
    }

    public function index(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $pageNum = (int)$request->getAttribute('page', 1);
        $response = $this->responseFactory->createResponse();
        /** @var UserRepository $repository */
        $repository = $orm->getRepository(User::class);

        $dataReader = $repository->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        $data = [
            'paginator' => $paginator,
        ];

        $output = $this->render('index', $data);

        $response->getBody()->write($output);
        return $response;
    }

    public function profile(Request $request, ORMInterface $orm): Response
    {
        $userRepo = $orm->getRepository(User::class);
        $login = $request->getAttribute('login', null);

        $item = $userRepo->findByLogin($login);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        $data = [
            'item' => $item,
        ];
        $response = $this->responseFactory->createResponse();

        $output = $this->render('profile', $data);
        $response->getBody()->write($output);

        return $response;
    }
}
