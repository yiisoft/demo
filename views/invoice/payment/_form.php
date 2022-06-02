<?php
    declare(strict_types=1); 
    
    use Yiisoft\Html\Html;
    use App\Invoice\Helpers\DateHelper;
    use DateTimeImmutable;
?>        
    <form method="post" id="form-payment" class="form-horizontal" action="<?= $urlGenerator->generate(...$action) ?>"  enctype="multipart/form-data">

    <input type="hidden" id="_csrf" name="_csrf" value="<?= $csrf ?>">   

    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('payment_form'); ?></h1>
        <?php
            $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]);
            echo (string)$response->getBody();
        ?>
    </div>

    <div id="content">
        <?= $alert; ?>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="inv_id" class="control-label" required><?= $s->trans('invoice'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="inv_id" id="inv_id" class="form-control" required <?= $edit ? 'readonly' : ''; ?>>
                            <?php foreach ($open_invs as $inv) { 
                                $inv_amount = $iaR->repoInvquery($inv->getId());                                        
                            ?>
                            <option value=""><?= $s->trans('none'); ?></option> 
                            <option value="<?=  $inv->getId(); ?>"
                                <?php $s->check_select($body['inv_id'] ?? '', $inv->getId()); ?>>
                                <?=  $inv->getNumber() . ' - ' . 
                                     $clienthelper->format_client($cR->repoClientquery($inv->getClient_id())) . 
                                     ' - ' . 
                                     $numberhelper->format_currency($inv_amount->getBalance()); 
                                ?>
                            </option>
                        <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <?php
                $pdate = $body['payment_date'] ?? new DateTimeImmutable('now');
                if ($pdate && $pdate !== "0000-00-00") {
                    //use the DateHelper
                    $datehelper = new DateHelper($s);
                    $pdate = $datehelper->date_from_mysql($pdate);
                } else {
                    $pdate = null;
                }
            ?>
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_date" class="control-label" required><?= $s->trans('date'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="payment_date" id="payment_date" placeholder="<?= ' ('.$datehelper->display().')';?>"
                           class="form-control input-sm datepicker" readonly
                           value="<?php if ($pdate <> null) {echo Html::encode($pdate);} ?>" required role="presentation" autocomplete="off">
                    <span class="input-group-text">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="amount" class="control-label" required><?= $s->trans('amount'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">               
                <input type="number" name="amount" id="amount" class="form-control" min="0" step=".500" value="<?= $s->format_amount($body['amount'] ?? "0.001"); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label" required>
                    <?= $s->trans('payment_method'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6 payment-method-wrapper">                
                <select id="payment_method_id" name="payment_method_id" class="form-control" required>
                    <?php foreach ($payment_methods as $payment_method) { ?>
                        <option value="<?=  $payment_method->getId(); ?>"
                            <?php $s->check_select(Html::encode($body['payment_method_id'] ?? ''), $payment_method->getId()) ?>>
                            <?=  $payment_method->getName(); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="note" class="control-label" required><?= $s->trans('note'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="note" class="form-control" required><?=  $body['note'] ?? ''; ?></textarea>
            </div>
        </div>
        <?php foreach ($custom_fields as $custom_field): ?>            
        <div class="form-group">
        <?= $cvH->print_field_for_form($payment_custom_values,
                                       $custom_field,
                                       // Custom values to fill drop down list if a dropdown box has been created
                                       $custom_values, 
                                       // Class for div surrounding input
                                       'col-xs-12 col-sm-6',
                                       // Class surrounding above div
                                       'form-group',
                                       // Label class similar to above
                                       'control-label'); ?>
        </div>    
        <?php endforeach; ?>
        
    </div>  
    <?php $js64 = "$(function () {".
            '$(".form-control.input-sm.datepicker").datepicker({dateFormat:"'.$datehelper->datepicker().'"});'.
          '});';
          echo Html::script($js64)->type('module');
    ?>
    <?php $js65 = "$(function () {".
        '$("#payment_date.form-control.input-sm.datepicker").datepicker({dateFormat:"'.$datehelper->datepicker().'"});'.
      '});';
      echo Html::script($js65)->type('module');
    ?>
</form>
