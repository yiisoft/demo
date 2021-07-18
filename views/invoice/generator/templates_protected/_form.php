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

<?php echo '<form id="'.$generator->getCamelcase_capital_name().'Form" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">';?>

<?php echo '<input type="hidden" name="_csrf" value="<?= $csrf ?>">'; ?>

<?php echo '<div id="headerbar">'."\n"; ?>
<?php echo '<h1 class="headerbar-title">'.$generator->getCamelcase_capital_name().'Form</h1>'."\n"; ?>
<?php echo '<div id="content">'."\n";
      echo '<div class="row">'."\n";
      foreach ($relations as $relation){
          echo ' <div class="mb3 form-group">'."\n";
          echo '    <label for="'.$relation->getLowercase_name().'_id">';
          echo ucfirst(str_replace('_', ' ', $relation->getLowercase_name())).'</label>'."\n";
          echo '    <select name="'.$relation->getLowercase_name().'_id" id="'.$relation->getLowercase_name().'_id" class="form-control simple-select">'."\n";
          echo '       <option value="0">'. ucfirst(str_replace('_', ' ', $relation->getLowercase_name())).'</option>'."\n";
          echo '         <?php foreach ($'.$relation->getLowercase_name().'s as $'.$relation->getLowercase_name().') { ?>'."\n";
          echo '          <option value="<?= $'.$relation->getLowercase_name().'->id; ?>"'."\n";
          echo '           <?php $s->check_select(Html::encode($body['."'".$relation->getLowercase_name()."_id'] ?? ''), $".$relation->getLowercase_name().'->id) ?>'."\n";
          echo '           ><?= $'.$relation->getLowercase_name().'->'.$relation->getLowercase_name().'_name; ?></option>'."\n";
          echo '         <?php } ?>'."\n";
          echo '    </select>'."\n";
          echo ' </div>'."\n";
      } 
      foreach ($orm_schema->getColumns() as $column) {
        //if the column is not a relation column  
        if (substr($column, -3) <> '_id') { 
          echo ' <div class="mb3 form-group">'."\n";
          echo '   <label for="'.$column->getName().'">'.$column->getName();
          echo '</label>'."\n";
          echo '   <input type="text" name="'.$column->getName().'" id="'.$column->getName().'" class="form-control"'."\n";
          echo '      value="<?= Html::encode($body['."'".$column->getName()."'".'] ??'. " ''". '); ?>'.'"'.'>'."\n";
          echo ' </div>'."\n";
        }  
      }    
      echo '</div>'."\n";
      echo '</form>'."\n";
?>