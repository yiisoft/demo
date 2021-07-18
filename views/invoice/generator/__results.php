<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\VarDumper\VarDumper;
/**
 * @var \App\Invoice\Entity\Generator $generators
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $id
 * @var \Yiisoft\Session\Flash\Flash $flash 
 */

?>
    <h1><?= Html::encode('Generator'); ?></h1>
    <?php
      if (!empty($flash)) {
        $danger = $flash->get('danger');
        if ($danger !== null) {
            $alert =  Alert::widget()
                ->body($danger)
                ->options([
                    'class' => ['alert-danger shadow'],
                ])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info !== null) {
            $alert =  Alert::widget()
                ->body($info)
                ->options([
                    'class' => ['alert-info shadow'],
                ])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning !== null) {
            $alert =  Alert::widget()
                ->body($warning)
                ->options([
                    'class' => ['alert-warning shadow'],
                ])
            ->render();
            echo $alert;
        }
        $success = $flash->get('success');
        if ($success !== null) {
            $alert =  Alert::widget()
                ->body($success)
                ->options([
                    'class' => ['alert-success shadow'],
                ])
            ->render();
            echo $alert;
        }
      }
    ?>
    <div>        
        <?php
        if ($canEdit && $success) {
            $highlight = PHP_SAPI !== 'cli';
            VarDumper::dump($generated, 40, $highlight);
            echo $highlight ? '<br>' : PHP_EOL;          
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');