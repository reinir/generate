<div>
<?=$x->title?>: 
<?php
foreach ($x->values as $value) {
    echo "<label><input type='radio' id='{$x->field}' name='{$x->field}'>{$value}</label>";
}
?>
</div>
