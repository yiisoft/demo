<?php           
   //interal type = eg. appearing in mysql
   //abstract type = eg. doctrine/cycle appearing IN annotation
   //type = eg. doctrine/cycle appearing BELOW annotation
   echo "<?php\n";             
?>

declare(strict_types=1); 

namespace <?= $generator->getNamespace_path().'\Entity'; ?>;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
<?php foreach ($relations as $relation) { 
    echo 'use ' . $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $relation->getCamelcase_name().';'."\n"; 
} ?>

/**
 * @Entity(
 <?php 
        echo "\n".' * repository="' . $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name().DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name() .'Repository",'."\n";
        if (!empty($generator->isCreated_include()) ||
            !empty($generator->isUpdated_include()) || 
            !empty($generator->isModified_include())){
            {
               echo ' * mapper="'.$generator->getNamespace_path().DIRECTORY_SEPARATOR.'Entity'.DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().'Mapper,"'."\n";       
            }
        }
        if (!empty($generator->getConstrain_index_field())){
               echo ' * constrain="'.$generator->getNamespace_path().DIRECTORY_SEPARATOR.'Entity'.DIRECTORY_SEPARATOR.'Scope'.DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().'Scope"'."\n";       
        }       
 ?>
 * )
 */
 
 class <?= $generator->getCamelcase_capital_name()."\n"; ?>
 {
       
   <?php foreach ($relations as $relation) {
         echo "\n";
         echo '    /**'."\n";
         echo '     * @BelongsTo(target="'.$relation->getCamelcase_name().'", nullable=false, fkAction="NO ACTION")'."\n";
         echo '     *'."\n";
         echo '     * @var \Cycle\ORM\Promise\Reference|'.$relation->getCamelcase_name()."\n";
         echo '     */'."\n"; 
         echo '     private $'.$relation->getLowercase_name().' = null;'."\n";
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
                          break;
                       }
                       else {
                           $init = false;
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
                    $ab = '     * @Column(type="'.$column->getAbstractType().'")'."\n";
                //Same as primary but uses bigInteger to store its values.    
                case 'bigPrimary':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'")'."\n";
                //Boolean type, some databases store it as an integer (1/0).                        
                case 'boolean':
                {
                       if ($column->hasDefaultValue()) {
                           $default  = $column->getDefaultValue();
                       }
                       else {
                           $default = false;
                       }
                       $ab = '     * @Column(type="'.$column->getAbstractType().($column->hasDefaultvalue() ? ',default='.$default."')"  : ')')."\n";
                }
                //Database specific integer (usually 32 bits).    
                case 'integer':
                    $result = $column->getSize();
                    $ab = '     * @Column(type="'.$column->getAbstractType().'('.$result.')", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Small/tiny integer, check your DBMS to check its size.    
                case 'tinyInteger':
                    $result = $column->getSize();
                    $ab = '     * @Column(type="'.$column->getAbstractType().'('.$result.')", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Big/long integer (usually 64 bits), check your DBMS to check its size.    
                case 'bigInteger':
                    $result = $column->getSize();
                    $ab = '     * @Column(type="'.$column->getAbstractType().'('.$result.')", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //length:255] String with specified length, a perfect type for emails and usernames as it can be indexed.    
                Case 'string':
                    $result = $column->getSize();
                    $ab = '     * @Column(type="'.$column->getAbstractType().'('.$result.')", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Database specific type to store text data. Check DBMS to find size limitations.    
                case 'text':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Tiny text, same as "text" for most of the databases. Differs only in MySQL.    
                case 'tinyText':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Long text, same as "text" for most of the databases. Differs only in MySQL.    
                case 'longText':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //[Double precision number.] (https://en.wikipedia.org/wiki/Double-precision_floating-point_format)
                case 'double':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Single precision number, usually mapped into "real" type in the database.
                case 'float':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //precision, [scale:0]	Number with specified precision and scale.    
                case 'decimal':
                    $result = $column->getPrecision() .','. $column->getScale();
                    $ab = '     * @Column(type="'.$column->getAbstractType().'('.$result.')", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //To store specific date and time, DBAL will automatically force UTC timezone for such columns.    
                case 'datetime':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //To store date only, DBAL will automatically force UTC timezone for such columns.    
                case 'date':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //To store time only.    
                case 'time':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Timestamp without a timezone, DBAL will automatically convert incoming values into UTC timezone. 
                //Do not use such column in your objects to store time (use DateTime instead) as timestamps will behave very specific to select DBMS.    
                case 'timestamp':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //To store binary data. Check specific DBMS to find size limitations.    
                case 'binary':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Tiny binary, same as "binary" for most of the databases. Differs only in MySQL.    
                case 'tinyBinary':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //Long binary, same as "binary" for most of the databases. Differs only in MySQL.    
                case 'longBinary':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
                //To store JSON structures, usually mapped to "text", only Postgres supports it natively.    
                case 'json':
                    $ab = '     * @Column(type="'.$column->getAbstractType().'", nullable='.$nullable.($column->hasDefaultvalue() ? ',default='.$column->getDefaultvalue()."')"  : ')')."\n";
            }  
            echo '    /**'."\n";
            echo $ab;
            echo '     */'."\n";
            echo '     private '.$questionmark.$column->getType()." $".$column->getName(). ' =  '.$init.';'."\n";
            echo '     '."\n";
            $construct .= "     ".$column->getType()." $".$column->getName(). ' = '.$init.','."\n    ";
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
                echo '    public function get'.$relation->getCamelcase_name().'() : ?'.$relation->getCamelcase_name().' {'."\n";
                echo '      return $this->'.$relation->getLowercase_name().';'."\n";
                echo '    }'."\n";
            }
            
            foreach ($orm_schema->getColumns() as $column) {
                echo '    '."\n";
                echo '    public function get'.ucfirst($column->getName()).'(): '.($column->isNullable() ? $questionmark : ''). $column->getType()."\n";
                echo '    {'."\n";
                echo '      return $this->'.$column->getName().';'."\n";
                echo '    }'."\n";
                echo '    '."\n";
                echo '    public function set'.ucfirst($column->getName()).'('.$column->getType().' $'.$column->getName().') : void'."\n";
                echo '    {'."\n";
                echo '      $this->'.$column->getName().' =  $'.$column->getName().';'."\n";
                echo '    }'."\n";          
            }
    ?>
}
                     
    
