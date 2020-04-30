<?php

use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Assets\AssetManager;

/**
 * @var KeysetPaginator $data
 * @var UrlGeneratorInterface $urlGenerator
 * @var AssetManager $assetManager
 */

?>

<?php foreach ($data->read() as $comment) { ?>
    <div class="card mb-3" data-id="<?php echo $comment['id']; ?>">
        <div class="card-header">
            #<?php echo $comment['id']; ?> <?php echo $comment['created_at']->format('Y.m.d'); ?>
        </div>
        <div class="card-body">
            <p class="card-text"><?php echo $comment['content']; ?></p>
        </div>
    </div>
<?php } ?>

<?php if (!$data->isOnLastPage()) { ?>
    <div class="row load-more-comment-container">
        <div class="col-sm-12">
            <a class="load-more-comment btn btn-primary btn-lg btn-block"
               href="<?php echo $urlGenerator->generate('comment/index', ['next' => $data->getNextPageToken()]); ?>">
                show more
            </a>
        </div>
    </div>
<?php } ?>
