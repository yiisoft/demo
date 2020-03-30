<?php

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
echo Html::tag('p', 'Total users: ' . $paginator->getTotalItems(), ['class' => 'text-muted']);
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
/** @var \App\Entity\User $item */
foreach ($paginator->read() as $item) {
    echo Html::beginTag('tr');
    echo Html::beginTag('td');
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
    echo Html::endTag('td');
    echo Html::tag('td', $item->getCreatedAt()->format('r'));
    echo Html::endTag('tr');
}
?>
    </tbody>
</table>
<?php
if ($pagination->isRequired()) {
    echo $pagination;
}
