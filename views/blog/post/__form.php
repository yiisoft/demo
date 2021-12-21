<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var array $tags
 * @var string $title
 */

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}
?>

<h1><?= Html::encode($title) ?></h1>

<form id="postForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div class="mb-3">
        <label for="title" class="form-label required"><?= $translator->translate('layout.title') ?></label>
        <input type="text" class="form-control" name="title" id="title" placeholder="<?= $translator->translate('layout.title') ?>" value="<?= Html::encode($body['title'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label for="content" class="form-label required"><?= $translator->translate('layout.content') ?></label>
        <textarea class="form-control" name="content" id="content" placeholder="<?= $translator->translate('layout.content') ?>" required><?= Html::encode($body['content'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label for="addTag" class="form-label"><?= $translator->translate('layout.add.tag') ?></label>
        <input type="text" class="form-control" id="addTag" placeholder="<?= $translator->translate('layout.add.tag') ?>" value="">
        <?= Html::button(
            $translator->translate('layout.add'),
            ['class' => 'btn btn-primary mt-2', 'id' => 'addTagButton']
        ) ?>
        <div id="tags">
            <?php foreach ($body['tags'] ?? [] as $tag) : ?>
                <button type="button" class="btn btn-sm btn-info me-2 remove-tag">
                    <input type="hidden" name="tags[]" value="<?= Html::encode($tag) ?>">
                    <?= Html::encode($tag) ?><span class="btn-close ms-1"></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?= $translator->translate('layout.submit') ?></button>
</form>
