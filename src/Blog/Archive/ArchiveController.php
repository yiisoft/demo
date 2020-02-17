<?php

namespace App\Blog\Archive;

use App\Controller;
use App\Blog\Entity\Tag;
use App\Blog\Tag\TagRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;

final class ArchiveController extends Controller
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;

    protected function getId(): string
    {
        return 'blog/archive';
    }

    public function index(ArchiveRepository $archiveRepo): Response
    {
        return $this->render('index', ['archive' => $archiveRepo->getFullArchive()]);
    }

    public function monthlyArchive(Request $request, ORMInterface $orm, ArchiveRepository $archiveRepo): Response
    {
        /** @var TagRepository $postRepo */
        $tagRepo = $orm->getRepository(Tag::class);

        $pageNum = (int)$request->getAttribute('page', 1);
        $year = $request->getAttribute('year', null);
        $month = $request->getAttribute('month', null);

        $dataReader = $archiveRepo->getMonthlyArchive($year, $month);
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'year' => $year,
            'month' => $month,
            'paginator' => $paginator,
            'archive' => $archiveRepo->getFullArchive()->withLimit(12),
            'tags' => $tagRepo->getTagMentions(self::POPULAR_TAGS_COUNT),
        ];
        return $this->render('monthly-archive', $data);
    }

    public function yearlyArchive(Request $request, ArchiveRepository $archiveRepo): Response
    {
        $year = $request->getAttribute('year', null);

        $data = [
            'year' => $year,
            'items' => $archiveRepo->getYearlyArchive($year),
        ];
        return $this->render('yearly-archive', $data);
    }
}
