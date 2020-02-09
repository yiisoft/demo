<?php

namespace App\Blog\Archive;

use App\Controller;
use App\Blog\Entity\Tag;
use App\Blog\Tag\TagRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Router\UrlGeneratorInterface;

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
        $output = $this->render(__FUNCTION__, [
            'archive' => $archiveRepo->getFullArchive(),
        ]);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }

    public function monthlyArchive(
        Request $request,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator,
        ArchiveRepository $archiveRepo
    ): Response {
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
        $output = $this->render('monthly-archive', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }

    public function yearlyArchive(
        Request $request,
        ORMInterface $orm,
        ArchiveRepository $archiveRepo
    ): Response {
        $year = $request->getAttribute('year', null);

        $data = [
            'year' => $year,
            'items' => $archiveRepo->getYearlyArchive($year),
        ];
        $output = $this->render('yearly-archive', $data);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($output);
        return $response;
    }
}