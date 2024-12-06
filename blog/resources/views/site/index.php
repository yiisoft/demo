<?php

declare(strict_types=1);

use Yiisoft\Html\Tag\Div;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Bootstrap5\Carousel;
use Yiisoft\Yii\Bootstrap5\CarouselItem;

/**
 * @var TranslatorInterface $translator
 * @var WebView             $this
 */
$this->setTitle('Home');

echo Carousel::widget()
    ->captionTagName('h2')
    ->items(
        new CarouselItem(
            Div::tag()->addAttributes(['style' => 'height: 200px'])->addClass('d-block w-100 bg-info'),
            $translator->translate('home.caption.slide1'),
            $translator->translate('home.caption.placeholder1'),
            captionAttributes: ['class' => ['d-none', 'd-md-block']],
        ),
        new CarouselItem(
            Div::tag()->addAttributes(['style' => 'height: 200px'])->addClass('d-block w-100 bg-secondary'),
            $translator->translate('home.caption.slide2'),
            $translator->translate('home.caption.placeholder2'),
            captionAttributes: ['class' => ['d-none', 'd-md-block']],
        ),
        new CarouselItem(
            Div::tag()->addAttributes(['style' => 'height: 200px'])->addClass('d-block w-100 bg-dark'),
            $translator->translate('home.caption.slide3'),
            $translator->translate('home.caption.placeholder3'),
            captionAttributes: ['class' => ['d-none', 'd-md-block']],
        ),
    )
    ->showIndicators();
?>


<div class="card mt-3 col-md-8">
    <div class="card-body">
        <h2 class="card-title"><?= $translator->translate('layout.console') ?></h2>
        <?php $binPath = str_replace('/', DIRECTORY_SEPARATOR, './yii'); ?>
        <h4 class="card-title text-muted"><?= $translator->translate('layout.create.new-user') ?></h4>
        <div>
            <code><?= "{$binPath} user/create &lt;login&gt; &lt;password&gt; [isAdmin = 0]" ?></code>
        </div>
        <h4 class="card-title text-muted mt-2 mb-1"><?= $translator->translate('layout.rbac.assign-role') ?></h4>
        <div>
            <code><?= "{$binPath} user/assignRole &lt;role&gt; &lt;userId&gt;" ?></code>
        </div>
        <h4 class="card-title text-muted mt-2 mb-1"><?= $translator->translate('layout.add.random-content') ?></h4>
        <div>
            <code><?= "{$binPath} fixture/add [count = 10]" ?></code>
        </div>
        <h4 class="card-title text-muted mt-2 mb-1"><?= $translator->translate('layout.migrations') ?></h4>
        <div>
            <code><?= "{$binPath} migrate/create" ?></code>
            <br><code><?= "{$binPath} migrate/generate" ?></code>
            <br><code><?= "{$binPath} migrate/up" ?></code>
            <br><code><?= "{$binPath} migrate/down" ?></code>
            <br><code><?= "{$binPath} migrate/list" ?></code>
        </div>
        <h4 class="card-title text-muted mt-2 mb-1"><?= $translator->translate('layout.db.schema') ?></h4>
        <div>
            <code><?= "{$binPath} cycle/schema" ?></code>
            <br><code><?= "{$binPath} cycle/schema/php" ?></code>
            <br><code><?= "{$binPath} cycle/schema/clear" ?></code>
            <br><code><?= "{$binPath} cycle/schema/rebuild" ?></code>
        </div>
    </div>
</div>
