<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Archive\ArchiveRepository;
use App\Controller;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;

final class BlogController extends Controller
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;

    protected function getId(): string
    {
        return 'blog';
    }

    public function index(Request $request, ORMInterface $orm, ArchiveRepository $archiveRepo): Response
    {
        /** @var PostRepository $postRepo */
        $postRepo = $orm->getRepository(Post::class);
        /** @var TagRepository $postRepo */
        $tagRepo = $orm->getRepository(Tag::class);

        $pageNum = (int)$request->getAttribute('page', 1);

        $dataReader = $postRepo->findAllPreloaded();

        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'paginator' => $paginator,
            'archive' => $archiveRepo->getFullArchive()->withLimit(12),
            'tags' => $tagRepo->getTagMentions(self::POPULAR_TAGS_COUNT),
        ];
        $output = $this->render(__FUNCTION__, $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }
}
