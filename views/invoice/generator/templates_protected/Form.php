<?php         
   //interal type = eg. appearing in mysql
   //abstract type = eg. doctrine/cycle appearing IN annotation
   //type = eg. doctrine/cycle appearing BELOW annotation
   echo "<?php\n";             
?>

declare(strict_types=1);

namespace <?= $generator->getNamespace_path().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name(); ?>;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class <?= $generator->getCamelcase_capital_name();?>Form extends FormModel
{    
    <?php
    echo "\n";
    foreach ($orm_schema->getColumns() as $column) {
        if ($column->IsNullable()) { 
              $nullable = 'true'; 
              $questionmark = '?'; 
        } else {
              $nullable = 'false';
              $questionmark = '';
        }
        $init = '';
        switch ($column->getType()) {
            case 'string':
                // ''
                $init = '\'\'';
                break;
            case 'float':
                $init = 'null';
                break;
            case 'int':
                $init = 'null';
                break;
            case 'bool':
                {
                   if ($column->hasDefaultValue()) {
                      $init  = $column->getDefaultValue();
                      break;
                   }
                   else {
                       $init = false;
                       break;
                   }
                }
        }
        echo '    private '.$questionmark.$column->getType()." $".$column->getName(). '='.$init.';'."\n";
    }
    foreach ($orm_schema->getColumns() as $column) {
        echo "\n";
        echo '    public function get'.ucfirst($column->getName()).'() : '.$column->getType()."\n";
        echo '    {'."\n";
        echo '      return $this->'.$column->getName().';'."\n";
        echo '    }'."\n";
    }
    echo "\n";
    echo '    public function getFormName(): string'."\n";
    echo '    {'."\n";
    echo '      return '."''".';'."\n";
    echo '    }'."\n";
    echo "\n";
    echo '    public function getRules(): array';
    echo '    {'."\n";
    echo '      return [';
    echo "\n";
    foreach ($orm_schema->getColumns() as $column) {
        echo "        '".$column->getName()."' => ["."\n";
        echo '            Required::rule(),'."\n";
        echo '        ],'."\n";
    }
    echo '    ];'."\n";   
    echo '}'."\n";
    echo '}'."\n";
 ?>
