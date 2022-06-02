<?php 
   use Yiisoft\Strings\Inflector;
   echo "<?php\n";             
?>

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Yii\Bootstrap5\Modal;

/**
 * @var \App\Invoice\Entity\<?= $generator->getCamelcase_capital_name(); ?> $<?= $generator->getSmall_singular_name()."\n"; ?>
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $id
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

?>
<?php 
        $inf = new Inflector();
        echo '<h1>'.$inf->toSentence($generator->getPre_entity_table(),'UTF-8').'</h1>'."\n"; 
?>
<?php   echo "<?php\n"; ?>
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
        
<?php   
        echo "\n";  
        echo '?>'."\n"; 
        echo '<div>'."\n";       
        echo '<?php'."\n";
        echo '    if ($canEdit) {'."\n";
        echo "        echo Html::a('Add',"."\n";
        echo '        $urlGenerator->generate('."'".$generator->getSmall_singular_name().'/add'."'),"."\n";
        echo "            ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']"."\n";
        echo '     );'."\n";
        echo '    //list all the items'."\n";
        echo '    foreach ($'.$generator->getSmall_plural_name().' as $'.$generator->getSmall_singular_name().'){'."\n";
        echo '      echo Html::br();'."\n";
        echo '      $label = $'.$generator->getSmall_singular_name().'->getId() . " "'.';'."\n";
        echo '      echo Html::label($label);'."\n";
        echo "      echo Html::a('Edit',"."\n";
        echo '      $urlGenerator->generate('."'".$generator->getSmall_singular_name().'/edit'."', ["."'id'".' => $'.$generator->getSmall_singular_name().'->getId()]),'."\n";
        echo "            ['class' => 'btn btn-info btn-sm ms-2']"."\n";
        echo '          );'."\n";                
        echo "      echo Html::a('View',"."\n";
        echo '      $urlGenerator->generate('."'".$generator->getSmall_singular_name().'/view'."', ["."'id'".' => $'.$generator->getSmall_singular_name().'->getId()]),'."\n";
        echo "      ['class' => 'btn btn-warning btn-sm ms-2']"."\n";
        echo '             );'."\n";
        echo '      //modal delete button'."\n";
        echo '      echo Modal::widget()'."\n";
        echo "      ->title('Please confirm that you want to delete this record# '".".$".$generator->getSmall_singular_name().'->getId())'."\n";
        echo "      ->titleOptions(['class' => 'text-center'])"."\n";
        echo "      ->options(['class' => 'testMe'])"."\n";
        echo '      ->size(Modal::SIZE_SMALL)'."\n";        
        echo "      ->headerOptions(['class' => 'text-danger'])"."\n";
        echo "      ->bodyOptions(['class' => 'modal-body', 'style' => 'text-align:center;',])"."\n";
        echo "      ->footerOptions(['class' => 'text-dark'])"."\n";
        echo "      ->footer("."\n";
        echo '                  Html::button('."\n";
        echo "                  'Close',"."\n";
        echo '                  ['."\n";
        echo "                              'type' => 'button',"."\n";
        echo "                              'class' => ['btn btn-success btn-sm ms-2'],"."\n";
        echo "                              'data' => ["."\n";
        echo "                              'bs-dismiss' => 'modal',"."\n";
        echo '                   ],'."\n";
        echo '                   ]'."\n";
        echo '                   ).';                
        echo "                   Html::a('Yes Delete it Please ... I am sure!',"."\n";
        echo '                   $urlGenerator->generate('."'".$generator->getSmall_singular_name().'/delete'."', ["."'id'".' => $'.$generator->getSmall_singular_name().'->getId()]),'."\n";
        echo "                   ['class' => 'btn btn-danger btn-sm ms-2']"."\n";
        echo '                              )'."\n";
        echo '                        )'."\n";
        echo '      ->withoutCloseButton()'."\n";
        echo '      ->toggleButton(['."\n";
        echo "                      'class' => ['btn btn-danger btn-sm ms-2'],"."\n";
        echo "                      'label' => 'Delete',"."\n";
        echo '                      ])'."\n";
        echo '      ->begin();'."\n";
        echo "      echo '<p>Are you sure you want to delete this record? </p>';"."\n";
        echo "      echo Modal::end();"."\n";
        echo "      echo Html::br();"."\n";
        echo '    }'."\n";           
        echo '    }'."\n";      
        echo "?>"."\n"; 
        echo '</div>';  
?>
