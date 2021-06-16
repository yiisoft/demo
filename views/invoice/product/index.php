<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
/**
 * @var \App\Invoice\Entity\Product $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $product_id
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

?>
    <h1><?= Html::encode($s->trans('products')); ?></h1>
    <?php
        $danger = $flash->get('danger');
        if ($danger != null) {
            $alert =  Alert::widget()
                ->body($danger)
                ->options([
                    'class' => ['alert-danger shadow'],
                ])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info != null) {
            $alert =  Alert::widget()
                ->body($info)
                ->options([
                    'class' => ['alert-info shadow'],
                ])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning != null) {
            $alert =  Alert::widget()
                ->body($warning)
                ->options([
                    'class' => ['alert-warning shadow'],
                ])
            ->render();
            echo $alert;
        }
    ?>
    <div>        
        <?php
        if ($canEdit) {
            echo Html::a($s->trans('add_product'),
                $urlGenerator->generate('product/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
            //list all the products
            foreach ($products as $product){
                echo Html::br();
                $label = $product->id . " ";
                echo Html::label($label);
                //echo Html::a($product->product_name." ". $product->product_description,$urlGenerator->generate('product/view',['product_id' => $product->product_id]),['class' => 'btn btn-success btn-sm ms-2']);
                echo Html::a($s->trans('edit'),
                $urlGenerator->generate('product/edit', ['product_id' => $product->id]),
                ['class' => 'btn btn-info btn-sm ms-2']
                );                
                echo Html::a($s->trans('view'),
                $urlGenerator->generate('product/view',['product_id' => $product->id]),
                ['class' => 'btn btn-warning btn-sm ms-2']
                );
                echo Html::a($s->trans('delete'),
                $urlGenerator->generate('product/delete',['product_id' => $product->id]),
                ['class' => 'btn btn-danger btn-sm ms-2']
                );
                echo Html::br();
            }           
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');
?>
