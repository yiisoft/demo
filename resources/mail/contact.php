<?php declare(strict_types=1);
use Yiisoft\Html\Html;

/* @var string $name */
/* @var string $content */
?>

<p>
    <?php echo Html::encode($content) ?>
</p>

<p><?php echo Html::encode($name) ?></p>
