<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Archive\ArchiveRepository;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\ViewRenderer;
use App\Service\Web

final class BlogController
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;
    private const ARCHIVE_MONTHS_COUNT = 12; 
 

    public function __construct(
        private ViewRenderer $viewRenderer,
        private readonly WebControllerService $webService)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog');
    }

    public function index(
        PostRepository $postRepository,
        TagRepository $tagRepository,
        ArchiveRepository $archiveRepo,
        CurrentUser $currentUser,
        #[RouteArgument('page')] int $pageNum = 1,
    ): Response {
        $dataReader = $postRepository->findAllPreloaded();

         $offsetPaginator = (new OffsetPaginator($dataReader))
                ->withPageSize(self::PRODUCTS_PER_PAGE);
            $totalPage = $offsetPaginator->getTotalPages();
            if ($pageNum > $totalPage || $pageNum < 1) {
                return $this->webService->getNotFoundResponse();
            }
            $paginator =  $offsetPaginator
                ->withCurrentPage($pageNum); 

        $data = [
            'paginator' => $paginator,
            'archive' => $archiveRepo
                ->getFullArchive()
                ->withLimit(self::ARCHIVE_MONTHS_COUNT),
            'tags' => $tagRepository->getTagMentions(self::POPULAR_TAGS_COUNT),
            'isGuest' => $currentUser->isGuest(),
        ];

        return $this->viewRenderer->render('index', $data);
    }
}
