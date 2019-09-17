
<?php
if(!empty(Registry::i()->massage)):
?>
<div id="massage">
    <?=Registry::i()->massage?>
</div>
<?php
endif;
?>
<div id="create">
    <form id="create_clien" method="post">
        <p>
            <span>Идентификатор </span><input type="text" name="client[identifier]" />
        </p>
        <p>
        <span>Имя клиента </span><input type="text" name="client[name]" />
        </p>
        <p>
            <span>Компания </span><input type="text" name="client[company]" />
        </p>
        <p>
            <span>Имя проекта </span><input type="text" name="client[project_name]" />
        </p>
        <p>
            <span>Описание </span><textarea style="" name="client[description]"></textarea>
        </p>
        <p>
            <span>HOST </span><input type="text" name="client[host]" />
        </p>
        <p>
            <span>Почта клиента </span><input type="text" name="client[email]" />
        </p>
        <p>
            <span>Позиция </span><input type="text" name="client[position]" value="<?=(!empty($clients) AND $end = end($clients))? $end['position']+1 : 0 ?>" />
        </p>
        <input type="submit" name="new-client" value="Отправить" />
    </form>
</div>