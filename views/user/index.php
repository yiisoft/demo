<?php
/**
 * @var \App\Entity\User[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

echo Html::tag('h1', 'Users');
echo Html::tag('p', 'Total users: ' . count($items), ['class' => 'text-muted']);

foreach ($items as $item) {
    echo Html::a(
        Html::encode($item->getLogin()),
        $urlGenerator->generate('user/profile', ['login' => $item->getLogin()]),
        ['class' => 'btn btn-link']
    );
}
