<?php

declare(strict_types=1);

use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;

/**
 * @var KeysetPaginator $data
 * @var UrlGeneratorInterface $urlGenerator
 * @var AssetManager $assetManager
 * @var WebView $this
 */

$this->setTitle('Comments Feed');

?>
<h1><?= Html::encode($this->getTitle()) ?></h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9 comment-feed-container">
        <?= $this->render('_comments', ['data' => $data]) ?>
    </div>
</div>
