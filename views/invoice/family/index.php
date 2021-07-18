<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Yii\Bootstrap5\Modal;
/**
 * @var \App\Invoice\Entity\Family $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $family_id
 * @var \Yiisoft\Session\Flash\Flash $flash 
 */
?>
    <h1><?= Html::encode($s->trans('family')); ?></h1>
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
      }
    ?>
    <div>        
        <?php
        if ($canEdit) {
            echo Html::a('Add Family',
                $urlGenerator->generate('family/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
            //list all the settings
            foreach ($familys as $family){
                echo Html::br();
                $label = $family->id . " ";
                echo Html::label($label);
                echo Html::a($family->family_name,$urlGenerator->generate('family/view',['family_id' => $family->id]),['class' => 'btn btn-success btn-sm ms-2']);
                echo Html::a($s->trans('edit'),
                $urlGenerator->generate('family/edit', ['family_id' => $family->id]),
                ['class' => 'btn btn-info btn-sm ms-2']
                );                
                echo Html::a($s->trans('view'),
                $urlGenerator->generate('family/view',['family_id' => $family->id]),
                ['class' => 'btn btn-warning btn-sm ms-2']
                );
                
                //modal delete button
                echo Modal::widget()
                ->title('Please confirm that you want to delete this record')
                ->titleOptions(['class' => 'text-center'])
                ->options(['class' => 'testMe'])
                ->size(Modal::SIZE_SMALL)        
                ->headerOptions(['class' => 'text-danger'])
                ->bodyOptions(['class' => 'modal-body', 'style' => 'text-align:center;',])
                ->footerOptions(['class' => 'text-dark'])
                ->footer(
                                Html::button(
                                    'Close',
                                    [
                                        'type' => 'button',
                                        'class' => ['btn btn-success btn-sm ms-2'],
                                        'data' => [
                                            'bs-dismiss' => 'modal',
                                        ],
                                    ]
                                ) . "\n" .                
                                Html::a('Yes Delete it Please ... I am sure!',
                                $urlGenerator->generate('family/delete',['family_id' => $family->id]),
                                ['class' => 'btn btn-danger btn-sm ms-2']
                                )
                            )
                ->withoutCloseButton()
                ->toggleButton([
                                'class' => ['btn btn-danger btn-sm ms-2'],
                                'label' => $s->trans('delete'),
                            ])
                ->begin();
                echo '<p>Are you sure you want to delete this record? </p>';
                echo Modal::end();
                echo Html::br();
            }           
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');