<?php

namespace App\Controller;

use App\Blog\Entity\Post;
use App\Blog\Post\PostRepository;
use App\Blog\Widget\PostCard;
use App\Controller;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface;

class SiteController extends Controller
{
    protected function getId(): string
    {
        return 'site';
    }

    public function index(): ResponseInterface
    {
        return $this->render('index');
    }

    /**
     * Content rendered in a stream
     */
    public function stream(ORMInterface $orm): ResponseInterface
    {
        # disable output buffering
        for ($j = ob_get_level(), $i = 0; $i < $j; ++$i) {
            ob_end_flush();
        }
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

        $stream = new \App\GeneratorStream($generator());
        return $this->responseFactory->createResponse()->withBody($stream);
    }
}
