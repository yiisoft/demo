<?php

declare(strict_types=1);

/**
 * @var OffsetPaginator       $paginator;
 * @var Tag                   $item
 * @var TranslatorInterface   $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView               $this
 */

use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Widget\OffsetPagination;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$this->setTitle($item->getLabel());

$pagination = OffsetPagination::widget()
    ->paginator($paginator)
    ->urlGenerator(fn ($page) => $urlGenerator->generate(
        'blog/tag',
        ['label' => $item->getLabel(), 'page' => $page]
    ));
echo Html::tag('h1', Html::encode($item->getLabel()));
echo Html::openTag('ul');
/** @var Post $post */
foreach ($paginator->read() as $post) {
    echo Html::openTag('li', ['class' => 'text-muted']);
    echo Html::a(Html::encode($post->getTitle()), $urlGenerator->generate('blog/post', ['slug' => $post->getSlug()]));
    echo ' by ';
    $userLogin = $post
        ->getUser()
        ->getLogin();
    echo Html::a(Html::encode($userLogin), $urlGenerator->generate('user/profile', ['login' => $userLogin]));
    echo ' at ';
    echo Html::span($post
        ->getPublishedAt()
        ->format('H:i d.m.Y'));
    echo Html::closeTag('li');
}
echo Html::closeTag('ul');

if ($pagination->isRequired()) {
    echo $pagination;
}
