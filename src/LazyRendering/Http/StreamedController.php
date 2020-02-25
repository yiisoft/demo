<?php

namespace App\LazyRendering\Http;

use App\Blog\Entity\Post;
use App\Blog\Post\PostRepository;
use App\Blog\Widget\PostCard;
use App\LazyRendering\View\MainLayout;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Yiisoft\Html\Html;

class StreamedController extends BaseController
{
    public const ROUTE_NAME = 'lazy-render';

    protected $pageLayout = MainLayout::class;

    public function pageIndex(): iterable
    {
        foreach (get_class_methods($this) as $method) {
            $isPage = strpos($method, 'page') === 0;
            if (!$isPage || $method === __FUNCTION__) {
                continue;
            }
            $page =  substr($method, 4);
            $pageName = ltrim(preg_replace('/([A-Z][a-z])/u', ' $1', $page));

            yield '<li>'
                . "{$pageName} :: "
                . Html::a('Lazy', $this->urlGenerator->generate(static::ROUTE_NAME, ['page' => $page]))
                . ' :: '
                . Html::a(
                    'Classic Mode',
                    $this->urlGenerator->generate(static::ROUTE_NAME, ['page' => $page, 'forceBuffering' => 1])
                )
                . ' :: '
                . Html::a(
                    'Combined Mode',
                    $this->urlGenerator->generate(static::ROUTE_NAME, ['page' => $page, 'forceBuffering' => 2])
                )
                . '</li>';
        } ?>
        <div class="mt-5">
            <p>
                <b>Lazy Mode</b> - deferred rendering pages. Page renders as it emits.
            </p>
            <p>
                <b>Classic Mode</b> (imitation) - page is rendered and buffered before the response object is sent back
                via pipeline.
            </p>
            <p>
                <b>Combined Mode</b> - deferred rendering pages. First yielded value will be emitted immediately,
                the rest of the contents will be buffered and emitted in one piece
            </p>
            <p>
                <a href="https://github.com/yiisoft/yii-demo/pull/59">About this on GitHub</a>
            </p>
        </div>
        <?php
    }

    public function pageAllPosts(ORMInterface $orm, int $interval = 0): iterable
    {
        yield '<h1>Streamed out</h1>';
        $t1 = microtime(true);
        /** @var PostRepository $postRepo */
        $postRepo = $orm->getRepository(Post::class);
        $pages = $postRepo->findAllPreloaded();
        yield '<h2>Total posts: ' . $pages->count() . '</h2>';
        $t2 = microtime(true);
        yield '<h5>Time to a count query: ' . intval(1_000_000 * ($t2 - $t1)) . 'μs</h5>';

        $card = PostCard::widget();
        $t3 = microtime(true);

        # type $stream->read() instead of $stream->getIterator()
        # to disable lazy loading and load all posts in buffer:
        /** @var Post $post */
        foreach ($pages->getIterator() as $post) {
            $t4 = microtime(true);
            yield (string)$card->post($post)
                . '<h5>Getting item time: ' . intval(1_000_000 * ($t4 - $t3)) . 'μs</h5>';
            usleep($interval);
            $t3 = microtime(true);
        }
    }

    public function pageAllPostsWithInterval(ORMInterface $orm): iterable
    {
        # disable time limit
        set_time_limit(0);

        return $this->pageAllPosts($orm, 150_000);
    }

    public function pageFewPostsAndError(ORMInterface $orm): iterable
    {
        yield '<h1>Streamed out with error after 3 posts</h1>';
        /** @var PostRepository $postRepo */
        $postRepo = $orm->getRepository(Post::class);

        $pages = $postRepo->findAllPreloaded()->withLimit(3);
        yield '<h2>Total posts: ' . $pages->count() . '</h2>';

        $card = PostCard::widget();
        /** @var Post $post */
        foreach ($pages->read() as $post) {
            yield (string)$card->post($post);
        }

        throw new RuntimeException('Just error');
    }

    public function pagePageWithEchoAndOutputBufferingBetweenYields(): iterable
    {
        yield '<h1>Page With Echo And Output Buffering Between Yields</h1>';

        for ($i = 1, $j = 20; $i < $j; ++$i) {
            if ($i % 2 === 0) {
                yield "<div>{$i} - yielded</div>";
            } else {
                echo "<div>{$i} - <b>printed</b></div>";
            }
        }
    }

    public function pageDirectEchoWithoutBufferingBetweenYields(): ResponseInterface
    {
        $generator = function () {
            echo '<h1>Direct Echo Without Buffering Between Yields</h1>';

            for ($i = 1, $j = 20; $i < $j; ++$i) {
                if ($i % 2 === 0) {
                    yield "<div>{$i} - yielded</div>";
                } else {
                    echo "<div>{$i} - <b>printed</b></div>";
                }
            }
        };
        $stream = new GeneratorStream($generator());
        return $this->responseFactory->createResponse()->withBody($stream);
    }
}
