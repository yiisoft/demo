<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogController extends Controller
{
    protected function getId(): string
    {
        return 'blog';
    }

    public function index(PostRepository $repository): Response
    {
        $response = $this->responseFactory->createResponse();

        $data = [
            'items' => $repository->findLastPublic(),
            'archive' => $repository->getArchive(),
        ];

        $output = $this->render('index', $data);

        $response->getBody()->write($output);
        return $response;
    }

    public function page(Request $request, PostRepository $repository): Response
    {
        $slug = $request->getAttribute('slug', null);

        $item = $repository->findBySlug($slug);

        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        $data = [
            'item' => $item,
        ];

        if (!$item->isPublic()) {
            if ($item->getPublishedAt() == null) {
                return $this->responseFactory->createResponse(404);
            }
            $response = $this->responseFactory->createResponse();

            // todo: hidden post
            $output = $this->render('post', $data);
        } else {
            $response = $this->responseFactory->createResponse();

            $output = $this->render('post', $data);
        }
        $response->getBody()->write($output);
        return $response;
    }
}
