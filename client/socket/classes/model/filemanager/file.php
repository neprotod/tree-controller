<?php

class Model_Filemanager_File_Socket{
    public $file;
    function __construct(){
        if(Request::get('file'))
            $this->file = iconv('UTF-8','cp1251',Request::get('file'));
        $this->dir = Request::get('dir');
        $this->path = $this->dir.'/'.$this->file;
    }
    
    function open(){
        if(is_file($this->path)){
            $content = file_get_contents($this->path);
            
            if(isset($_POST['encode'])){
                $content = iconv($_POST['encode']['old'],$_POST['encode']['new'],$content);
            }
            
        }else{
            echo 'Не файл';
        }
        
        $fond = array(
            'content' => $content
        );
        
        echo View::factory('filemanager_file_file','socket',$fond);
    }
    
    function save(){
        if(isset($_POST['content'])){
            $tmp = $this->dir.'/'.'temp_'.$this->file;
            if(file_put_contents($tmp,html_entity_decode(Request::post('content')))){
                unlink($this->path);
                rename($tmp,$this->path);
            }else{
                return Registry::i()->massage = 'Ошибка записи';
            }
            Registry::i()->massage = 'Файл обнавлен';
        }
    }
}
