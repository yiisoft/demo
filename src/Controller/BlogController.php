<?php
namespace App\Controller;

use App\Controller;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spiral\Pagination\Paginator;
use Yiisoft\Router\UrlGeneratorInterface;

class BlogController extends Controller
{
    private const POSTS_PER_PAGE = 10;

    protected function getId(): string
    {
        return 'blog';
    }

    public function index(
        Request $request,
        PostRepository $repository,
        TagRepository $tagRepository,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);
        $year = $request->getAttribute('year', null);
        $month = $request->getAttribute('month', null);
        $isArchive = $year !== null && $month !== null;

        $postsQuery = $isArchive
            ? $repository->findArchivedPublic($year, $month)
            : $repository->findLastPublic();
        $paginator = (new Paginator(self::POSTS_PER_PAGE))->withPage($pageNum)->paginate($postsQuery);

        $data = [
            'items' => $postsQuery->fetchAll(),
            'paginator' => $paginator,
            'pageUrlGenerator' => $isArchive
                ? fn($page) => $urlGenerator->generate(
                    'blog/archive',
                    ['year' => $year, 'month' => $month, 'page' => $page]
                )
                : fn ($page) => $urlGenerator->generate('blog/index', ['page' => $page]),
            'archive' => $repository->getArchive(),
            'tags' => $tagRepository->getTagMentions(10),
        ];
        $output = $this->render('index', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }

    public function page(Request $request, PostRepository $repository): Response
    {
        $slug = $request->getAttribute('slug', null);

        $item = $repository->fullPostPage($slug, $this->user->isGuest() ? null : $this->user->getId());

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

            // todo: hidden post page
            $output = $this->render('post', $data);
        } else {
            $response = $this->responseFactory->createResponse();

            $output = $this->render('post', $data);
        }
        $response->getBody()->write($output);
        return $response;
    }

    public function tag(
        Request $request,
        TagRepository $tagRepo,
        PostRepository $postRepo,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        $label = $request->getAttribute('label', null);
        $pageNum = (int)$request->getAttribute('page', 1);

        $item = $tagRepo->findByLabel($label);

        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }
        // preloading of posts
        $postsQuery = $postRepo->findByTag($item->getId());
        $paginator = (new Paginator(self::POSTS_PER_PAGE))->withPage($pageNum)->paginate($postsQuery);

        $data = [
            'item' => $item,
            'posts' => $postsQuery->fetchAll(),
            'paginator' => $paginator,
            'pageUrlGenerator' => fn($page) => $urlGenerator->generate(
                'blog/tag',
                ['label' => $label, 'page' => $page]
            ),
        ];
        $output = $this->render('tag', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }
}
