<?php

namespace App\StreamedRendering\Http;

use App\Blog\Entity\Post;
use App\Blog\Post\PostRepository;
use App\Blog\Widget\PostCard;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Html\Html;
use Yiisoft\Router\FastRoute\UrlGenerator;

class StreamedController extends BaseController
{
    public const PAGE_ROUTE   = 'streamed';
    public const ACTION_ROUTE = 'streamedAction';

    public function pageIndex(UrlGenerator $urlGenerator)
    {
        foreach (get_class_methods($this) as $method) {
            $isPage = strpos($method, 'page') === 0;
            if (!$isPage || $method === __FUNCTION__) {
                continue;
            }
            $page = substr($method, 4);
            yield '<li>' . Html::a($page, $urlGenerator->generate(static::PAGE_ROUTE, ['page' => $page])) . '</li>';
        }
    }

    /**
     * All posts
     */
    public function pageAllPosts(ORMInterface $orm): ResponseInterface
    {
        # disable time limit
        set_time_limit(0);

        $generator = static function () use ($orm) {
            yield '<h1>Streamed out</h1>';
            $t1 = microtime(true);
            /** @var PostRepository $postRepo */
            $postRepo = $orm->getRepository(Post::class);
            $stream = $postRepo->findAllPreloaded();
            yield '<h2>Total posts: ' . $stream->count() . '</h2>';
            $t2 = microtime(true);
            yield '<h5>Time to a count query: ' . intval(1_000_000 * ($t2 - $t1)) . 'μs</h5>';

            /** @var Post $post */
            $card = PostCard::widget();
            $t3 = microtime(true);

            # type $stream->read() instead of $stream->getIterator()
            # to disable lazy loading and load all posts in buffer:
            foreach ($stream->getIterator() as $post) {
                $t4 = microtime(true);
                yield (string)$card->post($post)
                    . '<h5>Getting item time: ' . intval(1_000_000 * ($t4 - $t3)) . 'μs</h5>';
                usleep(100_000);
                $t3 = microtime(true);
            }
            # phpinfo
            ob_start();
            phpinfo();
            $info = ob_get_clean();
            yield '<h2>PHPINFO</h2>' . $info;
        };
        return $this->prepareResponse($generator());
    }
}
