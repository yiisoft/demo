<?php  
     echo "<?php\n";             
?>

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Invoice\Helpers\DateHelper;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 */

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}

<?php  
     echo "?>";             
?>

<?php echo '<h1><?= Html::encode($title) ?></h1>'; ?>

<?php echo '<form id="'.$generator->getCamelcase_capital_name().'Form" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">';?>

<?php echo '<input type="hidden" name="_csrf" value="<?= $csrf ?>">'; ?>

<?php echo '<div id="headerbar">'."\n"; ?>
<?php echo '<h1 class="headerbar-title"><?= $s->trans('."'".$generator->getSmall_plural_name().'_form'."'); ?>".'</h1>'."\n"; ?>
<?php echo '<?php $response = $head->renderPartial('."'invoice/layout/header_buttons',['s'=>".'$s, '."'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>"; ?>        
<?php echo '<?php echo (string)$response->getBody(); ?>'; ?>
<?php echo '<div id="content">'."\n";
      echo '<div class="row">'."\n";
      foreach ($relations as $relation){
          echo ' <div class="mb3 form-group">'."\n";
          echo '    <label for="'.$relation->getLowercase_name().'_id">';
          echo ucfirst(str_replace('_', ' ', $relation->getLowercase_name())).'</label>'."\n";
          echo '    <select name="'.$relation->getLowercase_name().'_id" id="'.$relation->getLowercase_name().'_id" class="form-control">'."\n";
          echo '       <option value="0">'. ucfirst(str_replace('_', ' ', $relation->getLowercase_name())).'</option>'."\n";
          echo '         <?php foreach ($'.$relation->getLowercase_name().'s as $'.$relation->getLowercase_name().') { ?>'."\n";
          echo '          <option value="<?= $'.$relation->getLowercase_name().'->getId(); ?>"'."\n";
          echo '           <?php $s->check_select(Html::encode($body['."'".$relation->getLowercase_name()."_id'] ?? ''), $".$relation->getLowercase_name().'->getId()) ?>'."\n";
          echo '           ><?= $'.$relation->getLowercase_name().'->'.$relation->getView_field_name().'; ?></option>'."\n";
          echo '         <?php } ?>'."\n";
          echo '    </select>'."\n";
          echo ' </div>'."\n";
      } 
      foreach ($orm_schema->getColumns() as $column) {
        //if the column is not a relation column  
        if (substr($column, -3) <> '_id') { 
         if (($column->getType() === 'bool')) {  
          echo ' <div class="mb3 form-group">'."\n";
          echo '   <label for="'.$column->getName().'" class="form-label"><?= $s->trans('."'".$column->getName()."'". '); ?>';
          echo '</label>'."\n";
          echo '   <input type="hidden" name="'.$column->getName().'" value="0">'."\n";
          echo '   <input type="checkbox" name="'.$column->getName().'" id="'.$column->getName().'" value="1"';
          echo ' <?php $s->check_select(Html::encode($body['."'".$column->getName()."'".'] ??'. "''".' ), 1, '."'=='".', true) ?>>'."\n";
          echo ' </div>'."\n";
         }
         
         if (($column->getType() === 'string') && (($column->getAbstractType() === 'date' )||($column->getAbstractType() === 'datetime')))
         {
            echo ' <div class="mb-3 form-group has-feedback">';
            echo ' <?php '; 
            echo ' $date = $body['."'".$column->getName()."'".'] ?? null; '."\n";
            echo '$datehelper = new DateHelper($s); '."\n";
            echo 'if ($date && $date !== "0000-00-00") { '."\n";
            echo '    $date = $datehelper->date_from_mysql($date); '."\n";
            echo '} else { '."\n";
            echo '    $date = null; '."\n";
            echo '} '."\n";
            echo '   ?>  '."\n";            
            echo '<label form-label for='.'"'.$column->getName().'"'.'><?= $s->trans('."'".$column->getName()."'".') ." (".  $datehelper->display().") "; ?></label>';
            echo '<div class="mb3 input-group"> '."\n";
            echo '<input type="text" name="'.$column->getName().'" id="'.$column->getName().'" placeholder="<?= $datehelper->display(); ?>" '."\n";
            echo '       class="form-control data-datepicker" '."\n";
            echo '       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> '."\n";
            echo '<span class="input-group-text"> '."\n";
            echo '<i class="fa fa-calendar fa-fw"></i> '."\n";
            echo ' </span> '."\n";
            echo '</div>'."\n";   
            echo '</div>  ';  
         } 
         
         if (($column->getType() === 'float') && ($column->getAbstractType() === 'decimal' )) {
            echo '<div class="form-group">'."\n";
            echo '  <label for="'.$column->getName().'"><?= $s->trans('."'".$column->getName()."'".'); ?></label>'."\n";
            echo '      <div class="input-group has-feedback">'."\n";
            echo '          <input type="text" name="'.$column->getName().'" id="'.$column->getName().'" class="form-control"'."\n";
            echo '              value="<?= $s->format_amount($body['."'".$column->getName()."'"."] ?? ''); ?>".'">'."\n";
            echo '              <span class="input-group-text"><?= $s->get_setting('."'currency_symbol'".'); ?></span>'."\n";
            echo '      </div>'."\n";
            echo '</div>'."\n";
          }
          
          if (($column->getType() === 'string') && ($column->getAbstractType() <> 'date' )) {
            echo ' <div class="mb3 form-group">'."\n";
            echo '   <label for="'.$column->getName().'"><?= $s->trans('."'".$column->getName()."'". '); ?>';
            echo '</label>'."\n";
            echo '   <input type="text" name="'.$column->getName().'" id="'.$column->getName().'" class="form-control"'."\n";
            echo ' value="<?= Html::encode($body['."'".$column->getName()."'".'] ?? '. " ''". '); ?>'.'"'.'>'."\n";
            echo ' </div>'."\n";
          }
          
          if (($column->getType() === 'int') && ($column->getAbstractType() <> 'date' ) && ($column->getAbstractType() <> 'primary' )) {
            echo ' <div class="mb3 form-group">'."\n";
            echo '   <label for="'.$column->getName().'"><?= $s->trans('."'".$column->getName()."'". '); ?>';
            echo '</label>'."\n";
            echo '   <input type="text" name="'.$column->getName().'" id="'.$column->getName().'" class="form-control"'."\n";
            echo ' value="<?= Html::encode($body['."'".$column->getName()."'".'] ?? '. " ''". '); ?>'.'"'.'>'."\n";
            echo ' </div>'."\n";
          }
          
          if (($column->getType() === 'int') && ($column->getAbstractType() === 'primary' )) {
            echo ' <div class="mb3 form-group">'."\n";
            echo '   <input type="hidden" name="'.$column->getName().'" id="'.$column->getName().'" class="form-control"'."\n";
            echo ' value="<?= Html::encode($body['."'".$column->getName()."'".'] ?? '. " ''". '); ?>'.'"'.'>'."\n";
            echo ' </div>'."\n";
          }  
        }
      }    
      echo "\n".'</div>'."\n";
      echo "\n".'</div>'."\n";
      echo "\n".'</div>'."\n";
      echo '</form>'."\n";
?>