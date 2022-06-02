<?php 
   echo "<?php\n"; 
   use Yiisoft\Strings\Inflector;
?>

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;

/**
 * @var \App\Invoice\Entity\<?= $generator->getCamelcase_capital_name(); ?> $<?= $generator->getSmall_singular_name()."\n"; ?>
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */
 
 $danger = $flash->get('danger');
        if ($danger != null) {
            $alert =  Alert::widget()
            ->body($danger)
            ->options(['class' => ['alert-danger shadow'],])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info != null) {
            $alert =  Alert::widget()
            ->body($info)
            ->options(['class' => ['alert-info shadow'],])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning != null) {
            $alert =  Alert::widget()
            ->body($warning)
            ->options(['class' => ['alert-warning shadow'],])
            ->render();
            echo $alert;
        }
?>
<?php 
  $inf = new Inflector();
  echo '<div>'."\n";
  echo ' <h5>'.$inf->toSentence($generator->getPre_entity_table(),'UTF-8').'</h5>'."\n";
  echo ' <a class="btn btn-success" href="<?= $urlGenerator->generate('."'".$generator->getSmall_singular_name()."/add'); ?>".'">'."\n";
  echo '      <i class="fa fa-plus"></i> <?= $s->trans('."'new'".'); ?>';
  echo ' </a>';
  echo '</div>'."\n";
?>

<?php   
  echo "<?php\n"; 
  echo '$pagination = OffsetPagination::widget()'."\n"; 
  echo '->paginator($paginator)'."\n"; 
  echo '->urlGenerator(fn ($page) => $urlGenerator->generate('."'".$generator->getSmall_singular_name().'/index'."'".', ['."'".'page'."'".' => $page]));';
?>

        
        
<?php   
        echo "\n";  
        echo '?>'."\n"; 
?>

<?php   echo "<?php\n"; ?>
                if ($pagination->isRequired()) {
                   echo $pagination;
                }
<?php   
        echo "\n";  
        echo '?>'."\n"; 
?> 
                
<?php 
        echo '<div class="table-responsive">'."\n";
        echo '<table class="table table-hover table-striped">'."\n";
        echo '   <thead>'."\n";
        echo '    <tr>'."\n";                
?>
                
<?php foreach ($orm_schema->getColumns() as $column) { 
        if ((substr($column, -3) <> '_id') && ($column->getName() <> 'id')) {
          echo '        <th><?= $s->trans('."'".$column->getName()."'); ?>".'</th>'."\n";
        }
}  
?>
                
<?php foreach ($relations as $relation){
        echo '        <th><?= $s->trans('."'".$relation->getLowercase_name()."'); ?>".'</th>'."\n"; 
}
?>

<?php 
        echo '        <th><?= $s->trans('."'options'); ?></th>"."\n"; 
        echo '    </tr>'."\n";
        echo '   </thead>'."\n";
        echo '<tbody>'."\n";
?>

<?php   echo '<?php foreach ($paginator->read() as $'.$generator->getSmall_singular_name().') { ?>'."\n";
        echo '     <tr>'."\n";
?>
                
<?php foreach ($orm_schema->getColumns() as $column) { 
        if ((substr($column, -3) <> '_id') && ($column->getName() <> 'id')) {
           echo '      <td><?= Html::encode($'.$generator->getSmall_singular_name().'->get'.ucfirst($column->getName()).'()); ?></td>'."\n";                                
        }
}
?>
                
<?php foreach ($relations as $relation){
            echo '        <td><?= Html::encode($'.$generator->getSmall_singular_name().'->get'.$relation->getCamelcase_name().'()->'.$relation->getView_field_name()."); ?>".'</td>'."\n"; 
}
?>

<?php       
            echo '        <td>'."\n";
            echo '          <div class="options btn-group">'."\n";
            echo '          <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">'."\n";
            echo '                <i class="fa fa-cog"></i>'."\n";
            echo '                <?= $s->trans('."'".'options'."'".'); ?>'."\n";
            echo '          </a>'."\n";
            echo '          <ul class="dropdown-menu">'."\n";
            echo '              <li>'."\n";
            echo '                  <a href="<?= $urlGenerator->generate('."'".$generator->getSmall_singular_name()."/edit'".',['."'".'id'."'".'=>$'.$generator->getSmall_singular_name().'->getId()]); ?>" style="text-decoration:none">';
            echo '                       <i class="fa fa-edit fa-margin"></i>'."\n";
            echo '                       <?= $s->trans('."'edit'".'); ?>'."\n";
            echo '                  </a>'."\n";
            echo '              </li>'."\n";
            echo '              <li>'."\n";
            echo '                  <a href="<?= $urlGenerator->generate('."'".$generator->getSmall_singular_name()."/view'".',['."'".'id'."'".'=>$'.$generator->getSmall_singular_name().'->getId()]); ?>" style="text-decoration:none">';
            echo '                       <i class="fa fa-eye fa-margin"></i>'."\n";
            echo '                       <?= $s->trans('."'view'".'); ?>'."\n";
            echo '                  </a>'."\n";
            echo '              </li>'."\n";           
            echo '              <li>'."\n";
            echo '                  <a href="<?= $urlGenerator->generate('."'".$generator->getSmall_singular_name()."/delete'".',['."'".'id'."'".'=>$'.$generator->getSmall_singular_name().'->getId()]); ?>" style="text-decoration:none">';
            echo '                       <i class="fa fa-trash fa-margin"></i>'."\n";
            echo '                       <?= $s->trans('."'delete'".'); ?>'."\n";
            echo '                  </a>'."\n";
            echo '              </li>'."\n";   
            echo '          </ul>'."\n";
            echo '          </div>'."\n";
            echo '         </td>'."\n";                
            echo '     </tr>'."\n";
            echo '<?php } ?>'."\n";
            echo '</tbody>'."\n";
            echo '</table>'."\n";
            echo '<?php'."\n";
            echo '    $pageSize = $paginator->getCurrentPageSize();'."\n";
            echo '    if ($pageSize > 0) {'."\n";
            echo '      echo Html::p('."\n";
            echo "        sprintf('Showing %s out of %s ".$generator->getSmall_plural_name()."'".', $pageSize, $paginator->getTotalItems()),'."\n";
            echo "        ['class' => 'text-muted']"."\n";
            echo '    );'."\n";
            echo '    } else {'."\n";
            echo "      echo Html::p("."'No records'".');'."\n";
            echo '    }'."\n";
            echo '?>'."\n";
            echo '</div>'."\n";
            echo '</div>'."\n";        