<?php

declare(strict_types=1);

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
    <div class="card mb-3" data-id="<?php echo $comment->getId(); ?>">
        <div class="card-header">
            #<?php echo $comment->getId() ?> <?php echo $comment->getCreatedAt()->format('Y.m.d') ?>
        </div>
        <div class="card-body">
            <p class="card-text"><?php echo Html::encode($comment->getContent()) ?></p>
        </div>
    </div>
<?php } ?>

<?php if (!$data->isOnLastPage()) { ?>
    <div class="row load-more-comment-container">
        <div class="col-sm-12">
            <a class="load-more-comment btn btn-primary btn-lg btn-block"
               href="<?php echo $urlGenerator->generate('blog/comment/index', ['next' => $data->getNextPageToken()]) ?>">
                show more
            </a>
        </div>
    </div>
<?php } ?>
