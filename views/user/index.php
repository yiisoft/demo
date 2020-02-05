<?php

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \App\Pagination\PaginationSet $paginationSet;
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

echo Html::tag('h1', 'Users');
echo Html::tag('p', 'Total users: ' . $paginationSet->getPaginator()->getTotalItems(), ['class' => 'text-muted']);

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
foreach ($paginationSet->getPaginator()->read() as $item) {
    echo Html::beginTag('tr');
    echo Html::beginTag('td');
    echo Html::a(
        Html::encode($item->getLogin()),
        $urlGenerator->generate('user/profile', ['login' => $item->getLogin()]),
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
if ($paginationSet->needToPaginate()) {
    echo $this->render('../blog/_pagination', ['paginationSet' => $paginationSet]);
}
