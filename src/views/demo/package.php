<?php

use app\helpers\Html;
use Yiisoft\Yii\Bootstrap4\Tabs;

/** @var array $package */
/** @var string $readme */
/** @var string $composer */
/** @var bool $hasDependencies */

$id = $package['id'];

$this->title = $id;

$this->params['breadcrumbs'][] = ['url' => ['site/packages'], 'label' => 'Packages'];
$this->params['breadcrumbs'][] = $id;

?>

<h1>yiisoft/<?= $id ?>
    <div class="float-right"></div>
</h1>

<?= Html::travisBadge($id, $package['travis']) ?>

<hr/>

<?php $metricsData = \app\widgets\MetricsWidget::widget(['package' => $package, 'metrics' => $metrics, 'packageDir' => $packageDir]) ?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Metrics',
            'content' => <<<HTML
<img src="/img/packages/$id/chart.svg"/>
<img src="/img/packages/$id/pyramid.svg"/>
$metricsData
HTML

        ],
        [
            'label' => 'composer.json',
            'content' => Html::tag('pre', $composer),
        ],
        [
            'label' => 'Dependencies',
            'content' => Html::dependenciesImg($id),
        ],
        [
            'label' => 'README',
            'content' => $readme,
        ]
    ]
]) ?>
