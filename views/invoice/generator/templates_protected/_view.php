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

<?php 
      echo '<div class="row">'."\n";
      foreach ($orm_schema->getColumns() as $column) {
        //if the column is not a relation column  
        if ((substr($column, -3) <> '_id') && ($column->getName() <> 'id')) {
         if ($column->getAbstractType() <> 'date') {   
          echo ' <div class="mb3 form-group">'."\n";
          echo '<label for="'.$column->getName().'" class="form-label" style="background:lightblue">'.'<?= $s'."->trans('".$column->getName()."'); ?>";
          echo '</label>'."\n";
          echo '   <?= Html::encode($body['."'".$column->getName()."'".'] ??'. " ''". '); ?>'."\n";
          echo ' </div>'."\n";
         }
         if ($column->getAbstractType() === 'date') {   
          echo '<div class="mb3 form-group">'."\n";
          echo '  <label for="'.$column->getName().'" class="form-label" style="background:lightblue">'.'<?= $s'."->trans('".$column->getName()."'); ?>";
          echo '  </label>'."\n";
          echo '<?php $date = $body['."'".$column->getName()."'".'];';
          echo ' if ($date && $date != "0000-00-00") {';
          echo '    $datehelper = new DateHelper($s);';
          echo '  $date = $datehelper->date_from_mysql($date);';
          echo '} else {';
          echo '  $date = null;';
          echo '}';
          echo '?>';
          echo '<?= Html::encode($date); ?>';
          echo '</div>'."\n";
         }
        } 
      }  
      foreach ($relations as $relation){
          echo ' <div class="mb3 form-group">'."\n";
          echo '   <label for="'.$relation->getLowercase_name().'_id" class="form-label" style="background:lightblue">'.'<?= $s'."->trans('".$relation->getLowercase_name()."'); ?>";
          echo '</label>'."\n";
          echo '   <?= $'.$generator->getSmall_singular_name().'->get'.$relation->getCamelcase_name().'()->'.$relation->getView_field_name().';?>'."\n";
          echo ' </div>'."\n";
      }
      echo '</div>'."\n";
?>