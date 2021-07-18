<?php
 if (!$isGuest ) {
  foreach ($tables as $table) {
    echo '<div>';  
    $schema = $table->getSchema();
    echo '<br>';
    echo '<H1>'.$table->getName().'</H1>';
    echo '<table class="table">';
    echo '<thead>';
    echo '<tr><th scope="row">Name</th><th scope="row">Internal Type<th scope="row">Abstract Type</th><th scope="row">Type</th><th scope="row">Has Def Val</th><th scope="row">Def Value</th><th scope="row">Size</th><th scope="row">Precision</th><th scope="row">Scale</th><th scope="row">Nullable</th><th scope="row">Enums</th><th scope="row">Constraints</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($schema->getColumns() as $column) {
            echo '<tr>';
            echo "<td>{$column->getName()}</td>";
            echo "<td>{$column->getInternalType()}</td>";
            echo "<td>{$column->getAbstractType()}</td>";
            echo "<td>{$column->getType()}</td>";          // PHP type: int, float, string, bool
            echo "<td>{$column->hasDefaultValue()}</td>";
            echo "<td>{$column->getDefaultValue()}</td>";
            echo "<td>{$column->getSize()}</td>";
            echo "<td>{$column->getPrecision()}</td>";     // Decimals only
            echo "<td>{$column->getScale()}</td><td>{$column->isNullable()}</td>";
            $temp = '';
            foreach ($column->getEnumValues() as $enum) {
                $temp = $enum;
                $temp .= " ".$temp;
            }
            echo '<td>'.$temp.'</td>';
            $var = '';
            foreach ($column->getConstraints() as $constraint) {
                $var = $constraint;
                $var.= " ".$var;
            }
            echo '<td>'.$temp.'</td>';
            echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
}  
?>
