<div>
<?=$x->title?>: 
<select class="<?=$x->class?>" id="<?=$x->field?>" name="<?=$x->field?>">
<?php
foreach ($x->values as $value) {
    echo "<option>{$value}</option>";
}
?>
</select>
</div>
