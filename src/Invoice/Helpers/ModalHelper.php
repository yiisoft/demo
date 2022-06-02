<?php

declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository as SRepo;
use Yiisoft\Yii\Bootstrap5\Modal;
use Yiisoft\Html\Html;

Class ModalHelper
{

private SRepo $s;
    
public function __construct(SRepo $s)
{
    $this->s = $s;
}

// Eg. 
// $modalhelper->modal('quote_change_client btn-lg btn-outline-light','#w0-modal','fa fa-edit cursor-pointer small',null,'change_client','client');

public function modal($class, $target, $icon, $label, $title, $placeholdername, $keyboard=true)
{
    $this->s->load_settings();
    echo Modal::widget()
    ->title('')
    ->titleOptions(['class' => 'text-center'])
    ->options(['class' => 'col-xs-12 col-sm-10 col-sm-offset-1'])
    ->size(Modal::SIZE_LARGE)        
    ->headerOptions(['class' => 'text-danger'])
    ->bodyOptions(['class' => 'modal-body', 'style' => 'text-align:center;',])
    ->footerOptions(['class' => 'text-dark'])
    ->closeButton(['tag'=>'button','label'=>"&times;"])
    //btn_add_product will be used in invoice.js to populate the modal-placeholder below
    ->toggleButton([
                    'class' => [$class],
                    'data-bs-toggle'=>'modal',
                    'data-bs-keyboard'=>$keyboard,
                    'data-bs-target'=>$target,
                    'data-toggle'=>'tooltip',
                    'title'=>$this->s->trans($title),
                    'label' =>'<i class="'.$icon.'"></i>'. $this->s->trans($label)])
    ->begin();
    echo '<p></p>';
    echo '<div id="modal-placeholder-'.$placeholdername.'">';
    echo '<div><i class="fa fa-spin fa-spinner"></i></div>'; 
    echo '</div>';
    echo Modal::end();
    echo Html::br(); 
}                    

}