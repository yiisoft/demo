<?php

declare(strict_types=1);

use App\Blog\Entity\Comment;
use Yiisoft\Html\Html;

/**
 * @var Comment $data
 */
?>

<div class="card mb-3">
    <div class="bg-primary card-header text-white" data-id="<?= $data->getId(); ?>">
        #<?= $data->getId() ?>
        <?= $data->getUser()->getLogin() ?>
        <?= $data->getCreatedAt()->format('Y.m.d') ?>
    </div>

    <div class="card-body border border-primary">
        <p class="card-text"><?= Html::encode($data->getContent()) ?></p>
    </div>
</div>
