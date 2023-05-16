<?php

declare(strict_types=1);

namespace App\Blog\Archive;

use App\Blog\Tag\TagRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Yii\View\ViewRenderer;

final class ArchiveController
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('blog/archive');
    }

    public function index(ArchiveRepository $archiveRepo): Response
    {
        return $this->viewRenderer->render('index', ['archive' => $archiveRepo->getFullArchive()]);
    }

    public function monthlyArchive(
        TagRepository $tagRepository,
        ArchiveRepository $archiveRepo,
        #[RouteArgument('page')] int $pageNum = 1,
        #[RouteArgument('year')] int $year = 0,
        #[RouteArgument('month')] int $month = 0,
    ): Response {
        $dataReader = $archiveRepo->getMonthlyArchive($year, $month);
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'year' => $year,
            'month' => $month,
            'paginator' => $paginator,
            'archive' => $archiveRepo
                ->getFullArchive()
                ->withLimit(12),
            'tags' => $tagRepository->getTagMentions(self::POPULAR_TAGS_COUNT),
        ];

        return $this->viewRenderer->render('monthly-archive', $data);
    }

    public function yearlyArchive(ArchiveRepository $archiveRepo, #[RouteArgument('year')] int $year = 0): Response
    {
        $data = [
            'year' => $year,
            'items' => $archiveRepo->getYearlyArchive($year),
        ];

        return $this->viewRenderer->render('yearly-archive', $data);
    }
}
