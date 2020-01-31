<?php

namespace App\Blog;

use App\Controller;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Blog\Post\PostRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Router\UrlGeneratorInterface;

class BlogController extends Controller
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;

    protected function getId(): string
    {
        return 'blog';
    }

    public function index(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        /** @var PostRepository $postRepo */
        $postRepo = $orm->getRepository(Post::class);
        $tagRepo = $orm->getRepository(Tag::class);

        $pageNum = (int)$request->getAttribute('page', 1);
        $year = $request->getAttribute('year', null);
        $month = $request->getAttribute('month', null);
        $isArchive = $year !== null && $month !== null;

        $paginator = $isArchive
            ? $postRepo->findArchivedPublic($year, $month)
                       ->withTokenGenerator(fn ($page) => $urlGenerator->generate(
                           'blog/archive',
                           ['year' => $year, 'month' => $month, 'page' => $page]
                       ))
            : $postRepo->findLastPublic()
                       ->withTokenGenerator(fn ($page) => $urlGenerator->generate('blog/index', ['page' => $page]));

        $paginator = $paginator
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'paginator' => $paginator,
            'archive' => $postRepo->getArchive(),
            'tags' => $tagRepo->getTagMentions(self::POPULAR_TAGS_COUNT),
        ];
        $output = $this->render('index', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }
}
