<?php

use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;

/**
 * @var KeysetPaginator $data
 * @var UrlGeneratorInterface $urlGenerator
 * @var AssetManager $assetManager
 */

?>

<?php foreach ($data->read() as $comment) { ?>
    <div class="card mb-3" data-id="<?= $comment['id']; ?>">
        <div class="card-header">
            #<?= $comment['id'] ?> <?= $comment['created_at']->format('Y.m.d') ?>
        </div>
        <div class="card-body">
            <p class="card-text"><?= Html::encode($comment['content']) ?></p>
        </div>
    </div>
<?php } ?>

<?php if (!$data->isOnLastPage()) { ?>
    <div class="row load-more-comment-container">
        <div class="col-sm-12">
            <a class="load-more-comment btn btn-primary btn-lg btn-block"
               href="<?= $urlGenerator->generate('blog/comment/index', ['next' => $data->getNextPageToken()]) ?>">
                show more
            </a>
        </div>
    </div>
<?php } ?>
