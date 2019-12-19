<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends Controller
{
    protected function getId(): string
    {
        return 'user';
    }

    public function index(UserRepository $repository): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $data = [
            'items' => $repository->findAll(),
        ];

        $output = $this->render('index', $data);

        $response->getBody()->write($output);
        return $response;
    }
}
