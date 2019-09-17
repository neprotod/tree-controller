<?php
function parse($results,$margin = 0){
    ?>
    <div style="margin-left:<?=$margin?>px;">
    <?php
    foreach($results as $key => $result):
        if(is_array($result)){
            parse($result,$margin+20);
            continue;
        }
    ?>
        <div>
            <?=$key .' : '. $result?>
        </div>
    <?php
    endforeach;    
    ?>
    <hr />
    </div>
    <?php
}
function parse_form($results,$margin = 0,$database = NULL){
    foreach((array)$results as $key => $result):
        ?>
    <div style="margin-left:<?=$margin?>px;">
        <?php
        if(is_array($result))
            foreach($result as $name => $value):
        ?>
            <div>
                <?=$name .' : '. $value?>
            </div>
            <?php
                if($name == 'table_name'){
                    $name_table = $value;
                }
            ?>
    
    <?php
            endforeach;
        ?>
        <form method="post" style="display:inline;">
            <input name="table" type="hidden" value="<?=$name_table?>" />
            <input name="drop" type="submit" value="Drop" />
        </form>
        <form method="post" style="display:inline;">
            <input name="key" type="hidden" value="<?=$key?>" />
            <input name="table" type="hidden" value="<?=$name_table?>" />
            <input name="tree[get][file]" type="hidden" value="<?=$database.'_'.$name_table.date("d.m.Y")?>.sql" />
            <input name="save" type="submit" value="Save" />
        </form>
        <hr />
    </div>
        <?php
    endforeach;
}
/************************/
?>
<a href="/<?=Url::instance()?>">Обновить</a>
<br />
<br />
<div id="navigation">
    <h3>Навигация</h3>
    <a href="/<?=Registry::i()->host?>">К статусу</a>
    <br />
    <a href="/<?=Registry::i()->class_link?>/backup">К backup</a>
    <br />
</div>
<div id="box">
    <form method="post" style="float:left;">
        <p>База данных:    <?=$database?></p>
        <textarea id="sql" name="mysql[sql]"><?=Request::param($_POST['mysql']['sql'])?></textarea><br />
        <select name="mysql[type]">
            <option <?=($_POST['mysql']['type'] == '1')?'selected="selected"':'';?> value="1">SELECT</option>
            <option <?=($_POST['mysql']['type'] == '2')?'selected="selected"':'';?> value="2">INSERT</option>
            <option <?=($_POST['mysql']['type'] == '3')?'selected="selected"':'';?> value="3">UPDATE</option>
            <option <?=($_POST['mysql']['type'] == '4')?'selected="selected"':'';?> value="4">DELETE</option>
        </select>
        <input type="submit" value="Запрос" />
    </form>
    <div id="show_table">
        <?php
            if(Request::param($tables)){
                parse_form($tables,0,$database);
            }
        ?>
    </div>
<div style="clear:both;"></div>
</div>
<div id="result">
    <?php
        if(Request::param($result)){
            parse($result);
        }
    ?>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
    /* Вставляем tab при нажатии на tab в поле textarea
    ---------------------------------------------------------------- */
    $("textarea").keydown(function(event){
        // выходим если это не кропка tab
        if( event.keyCode != 9 )
            return;

        event.preventDefault();    

        // Opera, FireFox, Chrome
        var 
        obj = $(this)[0],
        start = obj.selectionStart,
        end = obj.selectionEnd,
        before = obj.value.substring(0, start), 
        after = obj.value.substring(end, obj.value.length);

        obj.value = before + "\t" + after;

        // устанавливаем курсор
        obj.setSelectionRange(start+1, start+1);
    });

});

</script>