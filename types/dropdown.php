<div>
<?=$x->title?>: 
<select id="<?=$x->field?>" name="<?=$x->field?>" class="<?=$x->class?>">
<?php
foreach ($x->values as $value) {
    echo "<option>{$value}</option>";
}
?>
</select>
</div>
