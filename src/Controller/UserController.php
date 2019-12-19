<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends Controller
{
    protected function getId(): string
    {
        return 'user';
    }

    public function index(UserRepository $repository): Response
    {
        $response = $this->responseFactory->createResponse();

        $data = [
            'items' => $repository->findAll(),
        ];

        $output = $this->render('index', $data);

        $response->getBody()->write($output);
        return $response;
    }

    public function profile(Request $request, UserRepository $repository): Response
    {
        $login = $request->getAttribute('login', null);

        $item = $repository->findByLogin($login);
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
