<?php

declare(strict_types=1);

use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var KeysetPaginator       $data
 * @var TranslatorInterface   $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var AssetManager          $assetManager
 */
?>

<?php foreach ($data->read() as $comment) { ?>
    <div class="card mb-3" data-id="<?= $comment->getId(); ?>">
        <div class="card-header">
            #<?= $comment->getId() ?> <?= $comment
                ->getCreatedAt()
                ->format('Y.m.d') ?>
        </div>
        <div class="card-body">
            <p class="card-text"><?= Html::encode($comment->getContent()) ?></p>
        </div>
    </div>
<?php } ?>

<?php if (!$data->isOnLastPage()) { ?>
    <div class="row load-more-comment-container">
        <div class="col-sm-12">
            <a class="load-more-comment btn btn-primary btn-lg btn-block"
               href="<?= $urlGenerator->generate('blog/comment/index', ['next' => $data->getToken()->value]) ?>">
                <?= $translator->translate('layout.show-more') ?>
            </a>
        </div>
    </div>
<?php } ?>
