<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Archive\ArchiveRepository;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\ViewRenderer;

final class BlogController
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;
    private const ARCHIVE_MONTHS_COUNT = 12;

    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog');
    }

    public function index(
        PostRepository $postRepository,
        TagRepository $tagRepository,
        ArchiveRepository $archiveRepo,
        CurrentUser $currentUser,
        CurrentRoute $currentRoute
    ): Response {
        $pageNum = (int)$currentRoute->getArgument('page', '1');
        $dataReader = $postRepository->findAllPreloaded();
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'paginator' => $paginator,
            'archive' => $archiveRepo->getFullArchive()->withLimit(self::ARCHIVE_MONTHS_COUNT),
            'tags' => $tagRepository->getTagMentions(self::POPULAR_TAGS_COUNT),
            'isGuest' => $currentUser->isGuest(),
        ];
        return $this->viewRenderer->render('index', $data);
    }
}
