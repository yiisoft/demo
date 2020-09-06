<?php

/**
 * @var $this \Yiisoft\View\View
 * @var $urlGenerator \Yiisoft\Router\UrlGeneratorInterface
 * @var $body array
 * @var $csrf string
 * @var $action string
 */

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap4\Alert;

if (!empty($error ?? '')) {
    echo Alert::widget()
        ->options(['class' => 'alert-danger'])
        ->body(
            Html::encode($error)
        );
}
?>


<h1><?= Html::encode($title) ?></h1>

<form id="postForm"
      method="POST"
      action="<?= $urlGenerator->generate(...$action) ?>"
      enctype="multipart/form-data"
>
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control" name="title" id="title" placeholder="Title"
               value="<?= Html::encode($body['title'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="content">Content</label>
        <textarea class="form-control" name="content" id="content"
                  placeholder="Content" required><?= Html::encode($body['content'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
