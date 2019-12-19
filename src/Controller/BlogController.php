<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogController extends Controller
{
    protected function getId(): string
    {
        return 'blog';
    }

    public function index(PostRepository $repository): ResponseInterface
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
