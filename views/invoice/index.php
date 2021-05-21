<?php
declare(strict_types=1);

use Yiisoft\Html\Html;

$this->setTitle($client);
?>
<h1><?= Html::encode($this->getTitle())?></h1>
<div class="row">
    <div class="col-sm-4 col-md-4 col-lg-3">
        <?php
        if (!$isGuest) {
            echo Html::a(
                'Add Client',
                $urlGenerator->generate('client/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
        } ?>
    </div>
</div>
