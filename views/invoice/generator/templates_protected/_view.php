<?php  
     echo "<?php\n";             
?>

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

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
        if (substr($column, -3) <> '_id') {   
          echo ' <div class="mb3 form-group">'."\n";
          echo '   <label for="'.$column->getName().'" class="form-label" style="background:lightblue">'.'<?= $s'."->trans('".$column->getName()."'); ?>";
          echo '</label>'."\n";
          echo '   <?= Html::encode($body['."'".$column->getName()."'".'] ??'. " ''". '); ?>'."\n";
          echo ' </div>'."\n";
        } 
      }  
      foreach ($relations as $relation){
          echo ' <div class="mb3 form-group">'."\n";
          echo '   <label for="'.$relation->getLowercase_name().'_id" class="form-label" style="background:lightblue">'.'<?= $s'."->trans('".$relation->getLowercase_name()."'); ?>";
          echo '</label>'."\n";
          echo '   <?= $'.$generator->getSmall_singular_name().'->get'.$relation->getCamelcase_name().'()->'.$relation->getLowercase_name().'_name'.';?>'."\n";
          echo ' </div>'."\n";
      }
      echo '</div>'."\n";
?>