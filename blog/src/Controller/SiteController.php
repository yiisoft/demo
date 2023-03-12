<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{
    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withController($this);
    }

    public function index(
        CacheInterface $cache
    ): ResponseInterface
    {
        $cache->get('123');
        $cache->set('123', ['1' => 2, new \stdClass()]);
        $cache->get('123');

        return $this->viewRenderer->render('index');
    }
}
