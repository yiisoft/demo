<?php
namespace App\Controller;

use App\Controller;
use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends Controller
{
    protected function getId(): string
    {
        return 'user';
    }

    public function index(ORMInterface $orm): Response
    {
        $repository = $orm->getRepository(User::class);
        $response = $this->responseFactory->createResponse();

        $data = [
            'items' => $repository->findAll(),
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
