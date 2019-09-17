<div>
    <a href="/<?=Url::instance()?>">Обновить</a>
    <br />
    <br />
    <div id="navigation">
        <h3>Навигация</h3>
        <a href="/<?=Registry::i()->class_link?>/directory<?=Url::query(array('file'=>NULL),'auto')?>">Назад</a>
        <br />
    </div>
    <div id="massage">
        <?=Request::param(Registry::i()->warning)?>
        <?=Request::param(Registry::i()->massage)?>
    </div>
    <br />
    <form method="post">
        <p>Перекодировать с:
            <select name="encode[old]">
                <option>cp1251</option>
                <option>UTF-8</option>
            </select>
            на:
            <select name="encode[new]">
                <option>UTF-8</option>
                <option>cp1251</option>
            </select>
            
            <input type="submit" value="Перекодировать" />
        </p>
    </form>
    <form method="post">
        <input type="submit" value="Сохранить" />
        <textarea style="display:block;" name="content"><?=$content?></textarea><br/>
        <input name="action" type="hidden" value="save" />
        <input type="submit" value="Сохранить" />
    </form>
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