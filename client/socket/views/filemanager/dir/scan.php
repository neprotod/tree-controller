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
    <form method="post" id="big_zip"  style="float:right;" action="/<?=Registry::i()->class_link?>/directory<?=Url::query(array('dir'=>Request::param($path,TRUE)),'auto')?>">
        <input class="big_zip" type="text" name="tree[big_zip][canonical]" />
        <input class="file" type="hidden" name="tree[big_zip][file]" />
        <input type="submit" value="Big zip" />
    </form>
    <form method="post" id="file_upload" enctype="multipart/form-data" style="float:right;">
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
<form method="post" id="main_form">
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
            <span style="padding-left:10px;" class="table-cell"><?=Registry::i()->size[$file]?></span>
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
    <?php
        if($path == '.'):
    ?>
    <input name="backup" type="submit" value="Backup" />
    <?php
        endif;
    ?>
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
/////////////////////////////////////////
var filemanager = {
    result_name : 'file_result',
    opacity_name : 'file_box',
    
    root : 'D:',
    
    first : '#big_zip .big_zip',
    file_name : '#big_zip .file',
    
    url : '/media/ajax/filemanager/index.php',
    
    init : function(){
        if(!(this).result){
            (this).result = $('<div id="'+(this).result_name+'"></div>').appendTo("body");
        }
        if(!(this).opacity){
            (this).opacity = $('<div id="'+(this).opacity_name+'" />').appendTo("body");
        }
        // Инициализируем функцию удаляения
        if((this).drop_init != true){
            (this).drop_init = true;
            var object = (this);
            (this).opacity.click(function(){
                object.drop()}
            );
        }
    },
    
    drop: function(){
        // указываем что бы загрузить снова
        (this).drop_init = false;
        
        // удаляем оэлемент и свойство
        (this).result.detach();
        delete (this).result;
        
        (this).opacity.detach();
        delete (this).opacity;
    },
    
    // для начального старта
    start : function(){
        var object = (this);
        $(object.first).click(function() {
            // инициализируем
            object.init();
            $.ajax({
                url: object.url,
                data: {
                    root: object.root
                },
                dataType: 'text',
                type: 'POST',
                success: function(data){
                    object.result.html(data);
                },
                error: function(xhr, textStatus, errorThrown){
                    alert("Error: " +textStatus);
                }
            });
        });
    },
    
    getFolder : function(folder){

        var object = (this);
        $.ajax({
                url: object.url,
                data: {
                    root: object.root, 
                    session_id : object.session_id, 
                    dir: folder,
                    original: object.original,
                    resize: object.resize
                },
                dataType: 'text',
                type: 'POST',
                success: function(data){
                    object.result.html(data);
                },
                error: function(xhr, textStatus, errorThrown){
                    alert("Error: " +textStatus);
                }
        });
    },
    getFile : function(canonical,file){
        if(!canonical){
            alert('Ошибка на странице');
            return;
        }
        var object = (this);
        (object).drop();
        (object).returns(canonical,file);
        
    },
    returns : function(canonical,file){
        var object = (this);
        $((object).first).val(canonical);
        $((object).file_name).val(file);
    }
}

filemanager.start();

function load($path,string){
    $($path).click(function(){
        $("body").append('<div style="position:fixed; top:0;bottom:0;left:0;right:0; background:#000;opacity:0.5;"></div><div style="position:fixed; top:45%;left:50%; font-size:20px; color:#fff;"><div style="margin-left:-50%;">'+string+'</div></div>');
    });
}
// Отображение загрузок
load('#big_zip input[type=submit]','Загружаем Big ZIP');
load('#file_upload input[type=submit]','Загружаем файл');
load('#main_form input[name=archivate]','Идет архивация');
load('#main_form input[name=de_archivate]','Идет разархивация');
load('#bufer input[name=past]','Копирование файла');

// Для backup
$("#main_form input[name=backup]").click(function(){
    $("#main_form .checkbox").attr('checked', 'checked');
});
</script>