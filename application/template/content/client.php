<div>
    <a href="?client">Добавить клиента</a>
</div>
<?php
if(!empty(Registry::i()->massage)):
?>
<div id="massage">
    <?=Registry::i()->massage?>
</div>
<?php
endif;
?>
<div id="errors"></div>
<div id="client_list">
    <?php
    foreach((array)$clients as $key => $client):
    ?>
    <div class="client">
        <h2 host="<?=$client['host']?>"><span><?=$client['host']?></span></h2>
        <div class="iframe">
            <div class="close">
                <iframe id="frame_<?=$key?>" src="<?=$client['host']?>"></iframe>
                <div class="click"></div>
            </div>
        </div>
        <div class="information">
            <form method="post">
                <div class="table">
                    <input type="hidden" name="id" value="<?=$client['id']?>" />
                    <div class="box">
                        <span class="first">Идентификатор</span>
                        <span><input type="text" name="update[identifier]" value="<?=$client['identifier']?>" /></span>
                    </div>
                    <div class="box">
                        <span class="first">Имя клиента</span>
                        <span><input type="text" name="update[name]" value="<?=Request::param($client['name'],TRUE)?>" /></span>
                    </div>
                    <div class="box">
                        <span class="first">Компания</span>
                        <span><input type="text" name="update[company]" value="<?=Request::param($client['company'],TRUE)?>" /></span>
                    </div>
                    <div class="box">
                        <span class="first">Имя проекта</span>
                        <span><input type="text" name="update[project_name]" value="<?=Request::param($client['project_name'],TRUE)?>" /></span>
                    </div>
                    <div class="box">
                        <span class="first">Описание</span>
                        <span><textarea style="" name="update[description]" value="<?=$client['description']?>"></textarea></span>
                    </div>
                    <div class="box">
                        <span class="first">HOST</span>
                        <span><input type="text" name="update[host]" value="<?=$client['host']?>" /></span>
                    </div>
                    <div class="box">
                        <span class="first">Почта клиента</span>
                        <span><input type="text" name="update[email]" value="<?=$client['email']?>" /></span>
                    </div>
                    <div class="box">
                        <span class="first">Позиция</span>
                        <span><input type="text" name="update[position]" value="<?=$client['position']?>" /></span>
                    </div>
                </div>
                <input type="submit" name="update-client" value="Обнавить" />
                <input type="submit" name="drop-client" value="Удалить" />
            </form>
        </div>
        <div style="clear:both;"></div>
    </div>
    <?php
    endforeach;
    ?>
</div>
<script type="text/javascript">
/*var error = {
    
}*/
var error = new Array();

$iframe = $("iframe");

$iframe.css('background','url(/media/iframe/loadingBar.gif) no-repeat center #fff');

$iframe.on('load',function(){

    $(this).css('background','');
    
    // Берем информацию со страници
    var $information = $(this).contents().find('#status_information');
    var $status_id = $information.find('.status_id input').val();
    var $status_host = $information.find('.status_host .string').text();
    //Добавляем информацию
    if($status_host && $status_id)
    $('h2[host="'+$status_host+'"]').append('<p style="font-size:14px; font-weight:normal;" class="id_host">Идентификатор на сайте: <b>'+$status_id+'</b></p>');
    
    // проверяем на наличие ошибок
    if($(this).contents().find('#status_error').text()){
        $(this).parent(".close").append('<div class="error" style="    position:absolute;top:0;bottom:-5px;left:0;right:-5px;background:red;opacity:0.5;"></div>');
        var array = {};
        array[$(this).attr('id')] = 'error';
        error.push(array);
        $("#errors").html("<b>Ошибок: "+error.length+'</b>');
    }
    $(this).off('load');
});
// Для увиличения фрейма
var frame = {
    block : ".iframe", 
    $block_iframe : "",
    $iframe : "",
    $this_iframe : {},
    addClass : "open",
    $click : "",
    $addr : "",
    host : "<?=Core::$root_url?>",
    src : "",
    scroll : "",
    error_connect : "c3bc592b8a755ac3148c0e01271cf6dc",
    init : function(){
        var object = this;
        var trol = $((object).block).find(".click").click(function(){
            (object).$block_iframe = $(this).parents('.iframe');
            (object).click();
        });
    },
    drop : function($drop){
        var object = this;
        $('body').css('overflow','');
        $drop.remove();
        (object).$addr.remove();
        // Убераем отслеживания страниц
        (object).$iframe.off('load');
        
        (object).$block_iframe.removeClass((object).addClass);
        (object).$block_iframe.find('.close').append((object).$click);
        
        (object).$block_iframe.find('iframe').each(function(){
            var $frame = $(this);
            var attr = $frame.attr('id');
            var bool = false;
            if(error.length != 0)
                error.forEach(function(element,index){
                    if(typeof element == 'object')
                        for(key in element)
                            if(key == attr){
                                if(error.length != 1)
                                    error = error.splice(index,1);
                                else
                                    error = new Array();
                            }
                });
            if($frame.contents().find('#status_error').text()){
                $frame.parent(".close").append('<div class="error" style="    position:absolute;top:0;bottom:-5px;left:0;right:-5px;background:red;opacity:0.5;"></div>');
                array = {};
                array[$(this).attr('id')] = 'error';
                error.push(array);
            }
            $("#errors").html("<b>Ошибок: "+error.length+'</b>');
            
        });
    },
    click : function(iframe){
        var object = this;
        
        $('body').css('overflow','hidden');
        (object).$block_iframe.addClass((object).addClass);
        (object).$block_iframe.find('.error').remove();
        // Находим фрейм
        (object).$iframe = (object).$block_iframe.find('iframe');

        (object).$click = (object).$block_iframe.find('.click').detach();
        (object).$block_iframe.prepend('<div id="drop">Закрыть</div>');
        
        (object).$block_iframe.prepend('<div id="addr"><input type="text" value="" /><button class="submit">Перейти</button><button class="error_connect">Error</button></div>');
        (object).$addr = (object).$block_iframe.find('#addr');
        
        // Получаем начальный адрес
        (object).src = (object).$iframe.attr("src");
        if((object).$iframe[0].link){
            (object).$iframe.attr("src",(object).$iframe[0].link)
        }
        // Для минибраузерной строки
        (object).$iframe.on('load',function(){
            if(!this.addr){
                this.addr = (object).host+'/'+(object).src;
            }else{
                (object).src = this.addr;
            }
            var input = (object).$addr.find('input');
            var button = (object).$addr.find('.submit');
            var href = $(this).contents().get(0).location.href;
            this.link = href;
            href = href.replace(this.addr,'');
            input.val(href);
        });
        (object).$addr.find('.submit').click(function(){
            var href = (object).$addr.find('input').val();
            (object).$iframe.attr('src',(object).src+href);
        });
        (object).$addr.find('.error_connect').click(function(){
            (object).$iframe.attr('src',(object).src+'/'+(object).error_connect);
        });
        (object).$block_iframe.find('#drop').click(function(){
            (object).drop($(this));
        });
        // Отслеживаем Enter
        (object).$addr.keypress(function(eventObject){
            if(eventObject.which == '13'){
                var href = (object).$addr.find('input').val();
                (object).$iframe.attr('src',(object).src+href);
            }
        });
    },
    error : function(frame){
        var object = this;
        
        (object).$block_iframe.addClass((object).addClass);
        (object).$block_iframe.find('.error').remove();
        (object).$click = (object).$block_iframe.find('.click').detach();
        (object).$block_iframe.prepend('<div id="drop">Закрыть</div>');
        (object).$block_iframe.find('#drop').click(function(){
            (object).drop($(this));
        });
    }
}
frame.init();
</script>