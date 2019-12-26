<?php
/**
 * @var \App\Entity\User[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

echo Html::tag('p', 'Users count: ' . count($items));

foreach ($items as $item) {
    echo Html::a(
        Html::encode($item->getLogin()),
        $urlGenerator->generate('user/profile', ['login' => $item->getLogin()]),
        ['class' => 'btn btn-link']
    );
}
