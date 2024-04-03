<div>
<?=$x->title?>: 
<select id='<?=$x->field?> name='<?=$x->field?>'>
<?php
foreach ($x->values as $value) {
    echo "<option>{$value}</option>";
}
?>
</select>
</div>
