<?php
   echo "<?php\n";             
?>

declare(strict_types=1); 

namespace <?= $generator->getNamespace_path().'\Entity'; ?>;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
<?php foreach ($orm_schema->getColumns() as $column) { 
       if ($column->getAbstractType() === 'date' || $column->getAbstractType() === 'datetime'){ 
           echo 'use DateTime;'."\n";
           echo 'use DateTimeImmutable;'; 
           break;
       }
}
?>
<?php foreach ($relations as $relation) { 
    echo 'use ' . $generator->getNamespace_path() .DIRECTORY_SEPARATOR.'Entity'. DIRECTORY_SEPARATOR. $relation->getCamelcase_name().';'."\n"; 
} ?>
  
 <?php 
    echo '#[Entity(repository: '.DIRECTORY_SEPARATOR. $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name().DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name() .'Repository::class)]'."\n";
    if (!empty($generator->isCreated_include()) || !empty($generator->isUpdated_include()) || !empty($generator->isModified_include())){
        echo (!empty($generator->isCreated_include()) ? '#[Behavior\CreatedAt(field: '. "'". 'date_created'."',column:'".'date_created'.')]' : '');          
        echo (!empty($generator->isUpdated_include()) ? '#[Behavior\UpdatedAt(field: '. "'". 'date_updated'."',column:'".'date_updated'.')]' : '');          
        echo (!empty($generator->isModified_include()) ? '#[Behavior\ModifiedAt(field: '. "'". 'date_modified'."',column:'".'date_modified'.')]' : '');          
    }       
 ?>
 
 class <?= $generator->getCamelcase_capital_name()."\n"; ?>
 {
       
   <?php foreach ($relations as $relation) {
         echo '     #[BelongsTo(target:'.$relation->getCamelcase_name()."::class, nullable: false, fkAction:"."'NO ACTION'".  ")]"."\n";
         echo '     private ?'.$relation->getCamelcase_name()." $".$relation->getLowercase_name().' = null;'."\n";
         echo '    '."\n";
    } ?>
    
    <?php
        $construct = '';        
        foreach ($orm_schema->getColumns() as $column) {
            $result = '';
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
                          if ($init === 1) {$init = 'false';}
                          if ($init === 0) {$init = 'true';}
                          break;
                       }
                       else {
                          $init = 'false';
                          break;
                       }
                    }
            }
            $ab = '';
            $default = '';
            $result='';
            switch ($column->getAbstractType()) {
                //Special column type, usually mapped as integer + auto-incrementing flag and added as table primary index column. You can define only one primary column in your table (you can still create a compound primary key, see below).
                case 'primary':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType()."')]"."\n";
                    $ate_or_lic='private ';
                    break;
                //Same as primary but uses bigInteger to store its values.
                case 'bigPrimary':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType()."')]"."\n";
                    $ate_or_lic='private ';
                    break;
                //Boolean type, some databases store it as an integer (1/0).
                case 'boolean':
                {
                    if ($column->hasDefaultValue()) {
                           $default  = $column->getDefaultValue();
                       }
                       else {
                           $default = 'false';
                    }
                    $ab = '     #[Column(type:'."'".$column->getAbstractType()."'".($column->hasDefaultvalue() ? ',default:'.$default  : '').($column->isNullable() ? ',nullable: true'  : ',nullable: false').')]'."\n";
                    $ate_or_lic='private ';
                    break;
                }
                //Database specific integer (usually 32 bits).    
                case 'integer':
                    $result = $column->getSize();
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().'('.$result.")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Small/tiny integer, check your DBMS to check its size.    
                case 'tinyInteger':
                    $result = $column->getSize();
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().'('.$result.")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    break;
                //Big/long integer (usually 64 bits), check your DBMS to check its size.    
                case 'bigInteger':
                    $result = $column->getSize();
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().'('.$result.")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //length:255] String with specified length, a perfect type for emails and usernames as it can be indexed.    
                Case 'string':
                    $result = $column->getSize();
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().'('.$result.")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Database specific type to store text data. Check DBMS to find size limitations.    
                case 'text':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Tiny text, same as "text" for most of the databases. Differs only in MySQL.    
                case 'tinyText':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Long text, same as "text" for most of the databases. Differs only in MySQL.    
                case 'longText':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //[Double precision number.] (https://en.wikipedia.org/wiki/Double-precision_floating-point_format)
                case 'double':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Single precision number, usually mapped into "real" type in the database.
                case 'float':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //precision, [scale:0]	Number with specified precision and scale.    
                case 'decimal':
                    $result = $column->getPrecision() .','. $column->getScale();
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().'('.$result.")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //To store specific date and time, DBAL will automatically force UTC timezone for such columns.    
                case 'datetime':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //To store date only, DBAL will automatically force UTC timezone for such columns.    
                case 'date':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //To store time only.    
                case 'time':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.'"'.$column->getDefaultvalue().'"'.")]"  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Timestamp without a timezone, DBAL will automatically convert incoming values into UTC timezone. 
                //Do not use such column in your objects to store time (use DateTime instead) as timestamps will behave very specific to select DBMS.    
                case 'timestamp':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //To store binary data. Check specific DBMS to find size limitations.    
                case 'binary':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Tiny binary, same as "binary" for most of the databases. Differs only in MySQL.    
                case 'tinyBinary':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //Long binary, same as "binary" for most of the databases. Differs only in MySQL.    
                case 'longBinary':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                //To store JSON structures, usually mapped to "text", only Postgres supports it natively.    
                case 'json':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType().")', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
                case 'enum':
                    $ab = '     #[Column(type:'."'".$column->getAbstractType()."(-1,1)', nullable: ".$nullable.($column->hasDefaultvalue() ? ',default: '.$column->getDefaultvalue().')]'  : ')]')."\n";
                    $ate_or_lic='private ';
                    break;
            }
            echo $ab;            
            if ($init === 'null') {$questionmark = '?';}
            if ($column->getAbstractType() === 'boolean'){
             echo '     '.$ate_or_lic. $questionmark.$column->getType()." $".$column->getName(). ' =  '.$init.';'."\n";   
             $construct .= "     ".$column->getType()." $".$column->getName(). ' = '.$init.','."\n    ";
            }
            
            if ($column->getAbstractType() === 'datetime') {
              echo '     '.$ate_or_lic. 'DateTimeImmutable'." $".$column->getName().';'."\n";
              $construct .= "      $".$column->getName(). ' = '.$init.','."\n    "; 
            }
            
            if ($column->getAbstractType() === 'date') {
              echo '     '.$ate_or_lic. " $".$column->getName().';'."\n";
              $construct .= "      $".$column->getName(). ' = '.$init.','."\n    "; 
            }
            
            if (($column->getAbstractType() <> 'date') && ($column->getAbstractType() <> 'datetime') && ($column->getAbstractType() <> 'boolean')){
              echo '     '.$ate_or_lic. $questionmark.$column->getType()." $".$column->getName(). ' =  '.$init.';'."\n"; 
              $construct .= "     ".$column->getType()." $".$column->getName(). ' = '.$init.','."\n    ";
            }
            
            echo '     '."\n";
        }
            echo '     public function __construct('."\n";
            echo '     '.rtrim($construct,",\n    ")."\n";            
            echo '     )'."\n";
            echo '     {'."\n";
            foreach ($orm_schema->getColumns() as $column) {
                echo '         $this->'.$column->getName().'=$'.$column->getName().';'."\n";
            }
            echo '     }'."\n";
            
            foreach ($relations as $relation) {
                echo '    '."\n";
                echo '    public function get'.$relation->getCamelcase_name().'() : ?'.$relation->getCamelcase_name()."\n";
                echo ' {'."\n";
                echo '      return $this->'.$relation->getLowercase_name().';'."\n";
                echo '    }'."\n";
            }
            
            foreach ($orm_schema->getColumns() as $column) {
                echo '    '."\n";
                if (substr($column->getName(), -2) === 'id'){
                     echo '    public function get'.ucfirst($column->getName()).'(): '.($column->isNullable() ? $questionmark : ''). 'string'."\n";
                     echo '    {'."\n";                     
                     echo '     return (string)$this->'.$column->getName().';'."\n";                    
                }else{                    
                     echo '    public function get'.ucfirst($column->getName()).'(): '.($column->isNullable() ? $questionmark : ''). ($column->getAbstractType() === 'date' ? 'DateTimeImmutable' : $column->getType())."\n";
                     echo '    {'."\n";
                     echo ''.$column->getAbstractType() === 'date' ? '      if (isset($this->'.$column->getName().') && !empty($this->'.$column->getName().')){'."\n" : '';
                     echo '       return $this->'.$column->getName().';'."\n";
                     echo ''.$column->getAbstractType() === 'date' ? '     };'."\n" : '';
                     echo ''.$column->isNullable() && $column->getAbstractType() === 'date' ? '      if (empty($this->'.$column->getName().')){'."\n" : '';
                     echo ''.$column->isNullable() && $column->getAbstractType() === 'date' ? '        return $this->'.$column->getName().';'."\n" : '';
                }
                echo '    }'."\n";
                echo '    '."\n";
                echo '    public function set'.ucfirst($column->getName()).'('.($column->getAbstractType() === 'date' ? 'DateTime' : $column->getType()).' $'.$column->getName().') : void'."\n";
                echo '    {'."\n";
                echo '      $this->'.$column->getName().' =  $'.$column->getName().';'."\n";
                echo '    }'."\n";          
            }
    ?>
}