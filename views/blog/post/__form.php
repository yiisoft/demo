<?php

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
use Yiisoft\Yii\Bootstrap4\Alert;

if (!empty($error ?? '')) {
    echo Alert::widget()
        ->options(['class' => 'alert-danger'])
        ->body(
            Html::encode($error)
        );
}
?>

<script>
    function removeTag(element) {
        element.parentNode.parentNode.removeChild(element.parentNode);
    }
</script>

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

    <div class="form-group">
        <label for="AddTag">Add tag</label>
        <input type="text" class="form-control" id="AddTag" placeholder="Add tag" value="">
        <?= Html::button(
    'Add',
    ['class' => 'btn btn-primary mt-2', 'onclick' => <<<JS
                (function() {
                    var input = document.getElementById('AddTag');
                    if (input.value && !document.getElementById('tag' + input.value)) {
                        var tags = document.getElementById('tags');
                        tags.insertAdjacentHTML('beforeend', 
                        `<span class="badge badge-info mr-2" id='tag` + input.value + `'>
                            <input type="hidden" name="tags[]" value="` + input.value + `">
                            <span>` + input.value + `</span>
                            <svg 
                            width="2em" 
                            height="2em" 
                            viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                            onclick="removeTag(this)"
                            >
                              <path 
                              fill-rule="evenodd" 
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 
                              2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 
                              5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>`);
                        input.value = '';
                    }
                }());
            JS
            ]
) ?>
    </div>

    <div class="form-group" id="tags">
        <?php foreach ($tags as $tag): ?>
             <span class="badge badge-info mr-2" id="tag<?= $tag ?>">
                <input type="hidden" name="tags[]" value="<?= $tag ?>">
                <span><?= $tag ?></span>
                <svg
                width="2em"
                height="2em"
                viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                onclick="removeTag(this)"
                >
                  <path
                  fill-rule="evenodd"
                  d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647
                  2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646
                  5.354a.5.5 0 0 1 0-.708z"/>
               </svg>
            </span>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
