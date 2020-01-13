<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spiral\Pagination\Paginator;

class BlogController extends Controller
{
    private const POSTS_PER_PAGE = 3;

    protected function getId(): string
    {
        return 'blog';
    }

    public function index(Request $request, PostRepository $repository, TagRepository $tagRepository): Response
    {
        $response = $this->responseFactory->createResponse();

        $pageNum = (int)$request->getAttribute('page', 1);

        $postsQuery = $repository->findLastPublic(['user', 'tags']);
        $paginator = (new Paginator(self::POSTS_PER_PAGE))->withPage($pageNum)->paginate($postsQuery);

        $data = [
            'items' => $postsQuery->fetchAll(),
            'paginator' => $paginator,
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

        $item = $repository->findBySlug($slug, [
            'user',
            'tags',
            'comments' => [
                'where' => ['public' => '1']
            ],
            'comments.user',
        ]);

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
