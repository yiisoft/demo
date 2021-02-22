<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator;
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;

$pagination = OffsetPagination::widget()
                              ->paginator($paginator)
                              ->urlGenerator(fn ($page) => $urlGenerator->generate('user/index', ['page' => $page]));

echo Html::tag('h1', 'Users');
echo Html::p('Total users: ' . $paginator->getTotalItems(), ['class' => 'text-muted']);
echo Html::a(
    'API v1 Info',
    $urlGenerator->generate('api/info/v1'),
    ['class' => 'btn btn-link']
), '<br>';
echo Html::a(
    'API v2 Info',
    $urlGenerator->generate('api/info/v2'),
    ['class' => 'btn btn-link']
), '<br>';
echo Html::a(
    'API Users List Data',
    $urlGenerator->generate('api/user/index'),
    ['class' => 'btn btn-link']
), '<br>';
?>
<table class="table table-hover">
    <thead>
    <tr>
        <th scope="col">Name</th>
        <th scope="col">Created at</th>
    </tr>
    </thead>
    <tbody>
<?php
/** @var \App\User\User $item */
foreach ($paginator->read() as $item) {
    echo Html::openTag('tr');
    echo Html::openTag('td');
    echo Html::a(
        Html::encode($item->getLogin()),
        $urlGenerator->generate('user/profile', ['login' => $item->getLogin()]),
        ['class' => 'btn btn-link']
    );
    echo Html::a(
        Html::encode('API User Data'),
        $urlGenerator->generate('api/user/profile', ['login' => $item->getLogin()]),
        ['class' => 'btn btn-link']
    );
    echo Html::closeTag('td');
    echo Html::tag('td', $item->getCreatedAt()->format('r'));
    echo Html::closeTag('tr');
}
?>
    </tbody>
</table>
<?php
if ($pagination->isRequired()) {
    echo $pagination;
}
