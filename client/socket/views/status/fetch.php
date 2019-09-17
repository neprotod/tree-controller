<a href="/<?=Url::instance()?>">Обновить</a>
<br />
<br />
<div id="navigation">
    <h3>Навигация</h3>
    <a href="/<?=Registry::i()->host?>/filemanager">filemanager</a>
    <br />
    <a href="/<?=Registry::i()->host?>/mysql">mysql</a>
    <br />
    <a href="/<?=Registry::i()->host?>/error">error</a>
    <br />
</div>
<h1>Статус</h1>
<form method="post" id="status_information">
    <p>
        <span class="name">Версия: </span> 
        <span class="string"><?=Core::VERSION?></span>
    </p>

    <p class="status_mode">
        <span class="name">Режим:</span> 
        <select name="status[mode]">
             <option value="1" <?=(Core::$selected_mode == 1)?'selected="selected"':''?>>PRODUCTION</option>
             <option value="2" <?=(Core::$selected_mode == 2)?'selected="selected"':''?>>STAGING</option>
             <option value="3" <?=(Core::$selected_mode == 3)?'selected="selected"':''?>>TESTING</option>
             <option value="4" <?=(Core::$selected_mode == 4)?'selected="selected"':''?>>DEVELOPMENT</option>
        </select>
    </p>
    <p class="status_id">
        <span class="name">Идентификатор:</span> 
        <input type="text" name="status[id]" value="<?=Core::TREE_ID?>" />
    </p>
    <p class="status_host">
        <span class="name">Хост:</span>
        <span class="string"><?=$_SERVER['HTTP_HOST']?></span>
    </p>

    <input type="submit" value="Переключить" />
</form>
<?php
if(defined("Core::TREE_HOST")):
    $host = str_replace('www.','',$_SERVER['HTTP_HOST']);
    if(Core::TREE_HOST != $host):
?>
<form method="post" id="status_activate">
    <input type="submit" name="tree[activate]" value="Активировать" />
</form>
<?php
    endif;
else:
?>
<div>Удалили Core::TREE_HOST</div>
<?php
endif;
?>
<div id="view" class="">
    <span id="span_view" style="border-bottom:1px solid;color:blue;cursor:pointer;">Вывести на экран</span>
</div>
<div id="admin" class="">
    <span id="span_admin" style="border-bottom:1px solid;color:blue;cursor:pointer;">Административная панель</span>
</div>
<div style="float:left;border:1px solid #000; display:inline-block; padding:5px;">
    <p>Все подключенные модули:</p>
    <hr />
    <?php
    $modules = Module::module_path();
    foreach($modules as $module):
    Module::factory($module);
    ?>
    <p style=" padding-bottom:5px;border-bottom:1px solid #000;"><?=$module?> <span style="float:right;"><?=(defined("{$module}_Module::VERSION"))? " | версия: " . constant("{$module}_Module::VERSION") : ''?><span></p>
    <?php
    endforeach;
    ?>
    
</div>
<div id="status_error" style="">
    <p>Ошибки:</p>
    <hr />
    <?php
    $xml = Model::factory("error_xml","socket");
    if(!empty($xml->errors))
        foreach($xml->errors as $errors):
    ?>
    <div class="box">
        <p><b>Тип:</b> <?=$errors['type']?></p>
        <p><b>Код ошибка:</b> <?=$errors['code']?></p>
        <p><b>Сообщение:</b> <?=$errors['message']?></p>
        <p><b>Файл:</b> <?=$errors['file']?></p>
        <p><b>На линии:</b> <?=$errors['line']?></p>
        <p><b>Класс:</b> <?=$errors['class']?></p>
        <p><?=$errors['debug']?></p>
    </div>
    <?php
        endforeach;
    ?>
</div>
<div style="clear:both;"></div>
<script type="text/javascript">
var view = {
    view : '#view',
    $view : '',
    content : '',
    init : function(){
        var object = this;
        (object).$view = $((object).view);
        (object).content = (object).$view.html();
        (object).get();
        
    },
    get : function(){
        var object = this;
        (object).$view.find('#span_view').click(function(){
            (object).$view.addClass('open');
            (object).$view.html('<div class="box"><div class="close"><span class="addr"></span><span>Закрыть</span></div><iframe src="<?=Core::$root_url?>"></iframe></div>');
            $(""+(object).view+" .close span").click(function(){
                (object).$view.html((object).content);
                (object).$view.removeClass('open');
                (object).get();
                (object).addr();
            });
            (object).addr();
        });
    },
    addr : function(){
        var object = this;
        var iframe = (object).$view.find('iframe');
        var addr = (object).$view.find('.addr');
        //connect((object).$view);
        
    }
}
view.init();


var admin = {
    view : '#admin',
    $view : '',
    content : '',
    init : function(){
        var object = this;
        (object).$view = $((object).view);
        (object).content = (object).$view.html();
        (object).get();
        
    },
    get : function(){
        var object = this;
        (object).$view.find('#span_admin').click(function(){
            (object).$view.addClass('open');
            (object).$view.html('<div class="box"><div class="close"><span>Закрыть</span></div><iframe src="<?=Core::$root_url?>/<?=Core::TREE_ID?>"></iframe></div>');
            $(""+(object).view+" .close span").click(function(){
                (object).$view.html((object).content);
                (object).$view.removeClass('open');
                (object).get();
            });
        });
    }
}
admin.init();
</script>