<?php

/**
 * @var $this \Yiisoft\View\View
 * @var $urlGenerator \Yiisoft\Router\UrlGeneratorInterface
 * @var $body array
 * @var $csrf string
 */

use Yiisoft\Html\Html;
?>

<h1>Add Post</h1>

<form id="contactForm"
      method="POST"
      action="<?= $urlGenerator->generate('blog/add') ?>"
      enctype="multipart/form-data"
>
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div class="form-group">
        <label for="header">Header</label>
        <input type="text" class="form-control" name="header" id="header" placeholder="Header"
               value="<?= Html::encode($body['header'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="content">Content</label>
        <textarea class="form-control" name="content" id="content"
                  placeholder="Content" required><?= Html::encode($body['content'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>