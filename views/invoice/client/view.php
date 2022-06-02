<?php
declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title 
 * @var \Yiisoft\Session\Flash\FlashInterface $flash
 */

?>
<div class="panel panel-default">
<div class="panel-heading">
<input type="hidden" id="_csrf" name="_csrf" value="<?= $csrf ?>">   
</div>
<div id="client_custom_fields">
    <?php echo $client_custom_fields; ?>
</div>
</div>    
<div>     
<?php $js12 = "$(function () {".
        '$(".form-control.input-sm.datepicker").datepicker({dateFormat:"dd-mm-yy"});'.
      '});';
      echo Html::script($js12)->type('module');
?>
</div>