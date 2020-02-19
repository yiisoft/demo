<?php

/**
 * @var \Yiisoft\Yii\Cycle\DataReader\SelectDataReader $dataReader;
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Blog\Entity\Post;
use App\Blog\Widget\PostCard;
use Yiisoft\Html\Html;

?>
<h1>All pages</h1>
<?php
$totalCount = $dataReader->count();
if ($totalCount > 0) {
    echo Html::tag(
        'p',
        sprintf('Posts count: %d', $totalCount),
        ['class' => 'text-muted']
    );
} else {
    echo Html::tag('p', 'No records');
}
/** @var Post $item */
foreach ($dataReader->read() as $item) {
    echo PostCard::widget()->post($item);
}
