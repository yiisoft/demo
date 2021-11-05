<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator;
 * @var \App\Blog\Entity\Tag $item
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;

$this->setTitle($item->getLabel());

$pagination = OffsetPagination::widget()
                              ->paginator($paginator)
                              ->urlGenerator(fn ($page) => $urlGenerator->generate(
                                  'blog/tag',
                                  ['label' => $item->getLabel(), 'page' => $page]
                              ));
echo Html::tag('h1', Html::encode($item->getLabel()));
echo Html::openTag('ul');
/** @var \App\Blog\Entity\Post $post */
foreach ($paginator->read() as $post) {
    echo Html::openTag('li', ['class' => 'text-muted']);
    echo Html::a(Html::encode($post->getTitle()), $urlGenerator->generate('blog/post', ['slug' => $post->getSlug()]));
    echo ' by ';
    $userLogin = $post->getUser()->getLogin();
    echo Html::a(Html::encode($userLogin), $urlGenerator->generate('user/profile', ['login' => $userLogin]));
    echo ' at ';
    echo Html::span($post->getPublishedAt()->format('H:i d.m.Y'));
    echo Html::closeTag('li');
}
echo Html::closeTag('ul');

if ($pagination->isRequired()) {
    echo $pagination;
}
