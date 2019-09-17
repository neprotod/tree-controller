
<a href="/<?=Url::instance()?>">Обновить</a>
<br />
<br />
<div id="navigation">
    <h3>Навигация</h3>
    <a href="/<?=Registry::i()->class_link?>/">К mysql</a>
    <br />
</div>
<?php
if(isset(Registry::i()->massage)):
?>
<div id="massage">
    <?=Registry::i()->massage?>
<div>
<?php
endif;
?>
<form method="post">
    <input name="tree[get][file]" type="hidden" value="<?=$database.date("d.m.Y")?>.sql" />
    <input name="all" type="submit" value="Всю базу" />
</form>
<form id="table_selected" method="post">
    <p>Таблицы через запятую</p>
    <input class="table" name="table" type="text" value="" />
    <input class="file"    name="tree[get][file]" type="hidden" value="<?=$database.'_'?>collection<?=date("d.m.Y")?>.sql" />
    <input name="selected" type="submit" value="Выбрать" />
</form>
<form method="post" enctype="multipart/form-data">
    <p>Востановить базу данных</p>
    <input type="file" multiple="multiple" name="file[]" />
    <br /><br />
    <input type="submit" value="Восстановить" />
</form>
<div id="result">
    <?php
    if(!empty($show_table))
        foreach($show_table as $key => $show):
    ?>
    <div>
        <p alt="<?=$key?>"><?=$show['table_name']?></p>
    </div>
    <?php
        
         endforeach;
    ?>
</div>
<script type="text/javascript">
var table = new Object();
var $form = $('#table_selected');
$form.submit(function(){
    var parsing = pars(table);
    var $file = $form.find('.file')
    var val = $file.val();
    parsing = val.replace(/collection/g,parsing);
    $file.val(parsing);
});

$("#result p[alt]").click(function(){
    var alt = $(this).attr('alt');
    
    if($(this).hasClass('select')){
        $(this).removeClass('select');
        delete table[alt];
    }else{
        $(this).addClass('select');
        table[alt] = $(this).text();
    }
    $form.find('.table').val(pars(table));
    
});

function pars(object){
    var string = '';
    var first = true;
    
    for(elem in object){
        if(first === true){
            first = false;
            var chars = '';
        }else{
            var chars = ',';
        }
        string += chars+object[elem].toString();
    }
    return string;
}
</script>