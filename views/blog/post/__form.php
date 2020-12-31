<?php

declare(strict_types=1);

/**
 * @var $this \Yiisoft\View\View
 * @var $urlGenerator \Yiisoft\Router\UrlGeneratorInterface
 * @var $body array
 * @var $csrf string
 * @var $action string
 * @var $tags array
 * @var $title string
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
        <label for="title" class="form-label required">Title</label>
        <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?= Html::encode($body['title'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label for="content" class="form-label required">Content</label>
        <textarea class="form-control" name="content" id="content" placeholder="Content" required><?= Html::encode($body['content'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label for="addTag" class="form-label">Add tag</label>
        <input type="text" class="form-control" id="addTag" placeholder="Add tag" value="">
        <?= Html::button(
            'Add',
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

    <button type="submit" class="btn btn-primary">Submit</button>
</form>