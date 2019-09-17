<a href="/<?=Url::instance()?>">Обновить</a>
<br />
<br />
<div id="navigation">
    <h3>Навигация</h3>
    <a href="/<?=Registry::i()->host?>">К статусу</a>
    <br />
</div>
<div id="information">
    <?php
    $pathArr = explode('/',$path);
    $nav = '';
    foreach((array)$pathArr as $pathA):
    if($pathA == '.')
        $nav .= $pathA;
    else
        $nav .= '/'.$pathA;
    ?>
    <a style="float:left;" href="/<?=Registry::i()->class_link?>/directory<?=Url::query(array('dir'=>Request::param($nav,TRUE)),'auto')?>"><?=$pathA?>/</a>
    <?php
    endforeach;
    ?>
    <div style="clear:both;"></div>
    <?php
    if($back !== NULL):
    ?>
    <p><a href="/<?=Registry::i()->class_link?>/directory<?=Url::query(array('dir'=>Request::param($back,TRUE)),'auto')?>">Назад</a></p>
    <?php
    endif;
    ?>
    <form method="post" enctype="multipart/form-data" style="float:right;">
        <input type="file" multiple="multiple" name="file[]" />
        <input name="upload" type="submit" value="Залить" />
    </form>

    <form method="post" style="float:right;">
        <input type="text" name="create" />
        <input name="create_file" type="submit" value="Создать файл" />
    </form>
    <form method="post" style="float:right;">
        <input type="text" name="create" />
        <input name="create_dir" type="submit" value="Создать директорию" />
    </form>
    <?php
    if(isset($_SESSION['directory']['save'])):
    ?>
    <div id="bufer" style="">
        <form method="post">
            <p>Буфер сейчас содержит</p>
            <?php
            $key = key($_SESSION['directory']['save']);
            foreach((array)reset($_SESSION['directory']['save']) as $save):
            ?>
            <p><?=$key.'/'.$save?></p>
            <?php
            endforeach;
            ?>
            <input name="clear" type="submit" value="Очистить буфер" />
            <input name="past" type="submit" value="Вставить" />
            <input name="cut" type="submit" value="Врезать" />
        </form>
    </div>
    <?php
    endif;
    ?>
    <div style="clear:both;"></div>
    <hr />
</div>
<form method="post">
    <div id="directory" class="table">
        <?php
        if(!empty($directories))
        foreach($directories as $directory):
        ?>
        <p class="table-row">
            <a class="table-cell" href="/<?=Registry::i()->class_link?>/directory<?=Url::query(array('dir'=>Request::param($path.'/'.$directory,TRUE)),'auto')?>"><?=$directory?></a>
            <input class="checkbox table-cell" name="selected[<?=$path?>][]" type="checkbox" value="<?=$directory?>" />
            
            <a class="rename table-cell">Переименовать</a>
        </p>
        <?php
        endforeach;
        ?>
    </div>
    <h3>Файлы</h3>
    <div id="file" class="table">
        <?php
        if(!empty($files))
        foreach($files as $file):
        /*
        
        */
        ?>
        <p class="table-row">
            <a class="table-cell" href="/<?=Registry::i()->class_link?>/file<?=Url::query(array('dir'=>Request::param($path,TRUE),'file'=>Request::param($file,TRUE)),'auto')?>"><?=$file?></a>
            <input class="checkbox table-cell" name="selected[<?=$path?>][]" type="checkbox" value="<?=$file?>" />
        
            <a class="save table-cell">Скачать</a>
            <a class="rename table-cell">Переименовать</a>
            <?php
            if($pathinfo = pathinfo($file) AND ($pathinfo['extension'] == 'zip' OR $pathinfo['extension'] == 'rar' OR $pathinfo['extension'] == 'tag')):
            ?>
            <a href="<?=Core::$root_url.ltrim($path.'/'.$file,'.')?>" class="table-cell">Скачать как ссылку</a>
            <?php
            endif;
            ?>
        </p>
        <?php
        endforeach;
        ?>

    </div>
    <input name="save" type="submit" value="Скопировать" />
    <input name="unlink" type="submit" value="Удалить" />
    <input name="archiv_name" type="text" value="" />
    <input name="archivate" type="submit" value="Архивировать" />
    <input name="de_archivate" type="submit" value="Разархевировать" />
</form>
<form style="display:none;" method="post" id="get" action="/<?=Registry::i()->class_link?>/get<?=Url::query(array('dir'=>Request::param($path,TRUE),'file'=>Request::param($file,TRUE),'return'=>urlencode(Url::instance())),'auto')?>">
    <input class="file" type="hidden" name="tree[get][file]" /> 
    <input type="hidden" name="tree[get][dir]" value="<?=$path?>" /> 
    <input class="start" type="submit"/> 
</form>
<form style="display:none;" method="post" id="rename" action="/<?=Registry::i()->class_link?>/directory<?=Url::query(array('dir'=>Request::param($path,TRUE)),'auto')?>">
    <div class="box">
        <input class="old" type="hidden" name="old" /> 
        <input class="new" type="text" name="new" value="<?=$path?>" /> 
        <input class="start" name="rename" type="submit" value="Переименовать" /> 
    </div>
</form>

<script type="text/javascript">
    $("#file p .save").click(function(){
        var get = $(this).parent().find('input').val();
        var $get = $("#get");
        $get.find('.file').val(get);
        $get.submit();
    });
    $("div p .rename").click(function(){
        var get = $(this).parent().find('input').val();
        var $form = $("#rename");
        $form.css('display','block');
        $form.find('.old').val(get);
        $form.find('.new').val(get);
    });
</script>