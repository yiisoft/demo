<?php
namespace App\Controller;

use App\Controller;
use App\Entity\Post;
use App\Entity\Tag;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spiral\Pagination\Paginator;
use Yiisoft\Router\UrlGeneratorInterface;

class BlogController extends Controller
{
    private const POSTS_PER_PAGE = 10;
    private const POPULAR_TAGS_COUNT = 10;

    protected function getId(): string
    {
        return 'blog';
    }

    public function index(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        $postRepo = $orm->getRepository(Post::class);
        $tagRepo = $orm->getRepository(Tag::class);

        $pageNum = (int)$request->getAttribute('page', 1);
        $year = $request->getAttribute('year', null);
        $month = $request->getAttribute('month', null);
        $isArchive = $year !== null && $month !== null;

        $postsQuery = $isArchive
            ? $postRepo->findArchivedPublic($year, $month)
            : $postRepo->findLastPublic();
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
            'archive' => $postRepo->getArchive(),
            'tags' => $tagRepo->getTagMentions(self::POPULAR_TAGS_COUNT),
        ];
        $output = $this->render('index', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }

    public function page(Request $request, ORMInterface $orm): Response
    {
        $postRepo = $orm->getRepository(Post::class);
        $slug = $request->getAttribute('slug', null);

        $item = $postRepo->fullPostPage($slug, $this->user->isGuest() ? null : $this->user->getId());
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        $data = [
            'item' => $item,
        ];
        $output = $this->render('post', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }

    public function tag(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        $tagRepo = $orm->getRepository(Tag::class);
        $postRepo = $orm->getRepository(Post::class);
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
