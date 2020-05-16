<?php

use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Router\UrlGeneratorInterface;

/**
 * @var KeysetPaginator $data
 * @var UrlGeneratorInterface $urlGenerator
 * @var AssetManager $assetManager
 */

?>
<h1>Comments Feed</h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9 comment-feed-container">
        <?= $this->render('_comments', ['data' => $data]) ?>
    </div>
</div>
