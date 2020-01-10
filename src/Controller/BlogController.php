<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogController extends Controller
{
    protected function getId(): string
    {
        return 'blog';
    }

    public function index(PostRepository $repository, TagRepository $tagRepository): Response
    {
        $response = $this->responseFactory->createResponse();

        $data = [
            'items' => $repository->findLastPublic(['user', 'tags']),
            'archive' => $repository->getArchive(),
            'tags' => $tagRepository->getTagMentions(10),
        ];

        $output = $this->render('index', $data);

        $response->getBody()->write($output);
        return $response;
    }

    public function page(Request $request, PostRepository $repository): Response
    {
        $slug = $request->getAttribute('slug', null);

        $item = $repository->findBySlug($slug, ['user', 'tags']);

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

    public function tag(Request $request, TagRepository $repository): Response
    {
        $label = $request->getAttribute('label', null);

        $item = $repository->findByLabel($label, ['posts' => [
            'where' => ['public' => '1'],
        ]]);

        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        $data = [
            'item' => $item,
        ];

        $output = $this->render('tag', $data);
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }
}
