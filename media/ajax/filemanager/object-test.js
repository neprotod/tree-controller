/*Для загрузки изображений с сервера*/
var filemanager = {
    result_name : 'file_result',
    opacity_name : 'file_box',
    
    root : 'media/original',
    
    first : 'images_wizard',
    
    url : '<?=Url::root()?>/media/js/filemanager/index.php',
    url_image : '<?=Url::root()?>/media/js/filemanager/image.php',
    
    session_id : '<?=session_id()?>',
    
    img_width: '100',
    img_height: '100',
    
    new_image: '#new_image',
    
    product_id: '<?=$product['id']?>',
    original: '<?=Registry::i()->settings['original']?>',
    resize: '<?=Registry::i()->settings['resize']?>',
    
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
        $('#'+object.first+'').click(function() {
            // инициализируем
            object.init();
            $.ajax({
                url: object.url,
                data: {
                    root: object.root, 
                    session_id : object.session_id, 
                    start : object.product_id,
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
    getImage : function(image,cononical,dir){
        if(!image || !cononical){
            alert('Ошибка на странице');
            return;
        }
        var object = (this);
        $.ajax({
                url: object.url_image,
                data: {
                    dir: dir, 
                    cononical: cononical, 
                    session_id : object.session_id, 
                    image: image, 
                    width: object.img_width, 
                    height: object.img_height,
                    original: object.original,
                    resize: object.resize
                },
                dataType: 'json',
                type: 'POST',
                success: function(data){
                    object.drop();
                    object.returns(data);
                },
                error: function(xhr, textStatus, errorThrown){
                    alert("Error: " +textStatus);
                }
        });
    },
    
    getImages : function(arr_image){
        var object = (this);
        $.ajax({
                url: object.url_image,
                data: {
                image : arr_image, 
                session_id : object.session_id, 
                width : object.img_width, 
                height : object.img_height,
                original : '<?=Registry::i()->settings['original']?>',
                resize : '<?=Registry::i()->settings['resize']?>'},
                dataType : 'json',
                type : 'POST',
                success: function(datas){
                    object.drop();
                    for(data in datas){
                        object.returns(datas[data]);
                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    alert("Error getImages: " +textStatus);
                }
        });
    },
    returns : function(data){
        var object = (this);
        $('<li><img src="'+data.src+'" /><span>'+data.image+'</span><a class="delete" href="#">Удалить</a><input type="hidden" value="'+data.image+'" name="new_images[]" /></li>').appendTo(""+object.new_image+"");
    }
}

filemanager.start();