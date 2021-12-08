<?php

declare(strict_types=1);

use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;

/**
 * @var KeysetPaginator $data
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var AssetManager $assetManager
 * @var WebView $this
 */

$this->setTitle($translator->translate('menu.comments-feed'));

?>
<h1><?= Html::encode($this->getTitle()) ?></h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9 comment-feed-container">
        <?= $this->render('_comments', ['data' => $data]) ?>
    </div>
</div>
