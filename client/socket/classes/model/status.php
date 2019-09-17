<?php

class Model_Status_Socket{
    function __construct(){}
    
    function fetch(){
        if(isset($_POST['status'])){
            // Изменение режима
            if(isset($_POST['status']['mode']) AND $_POST['status']['mode'] != Core::$selected_mode)
                $this->update_mode($_POST['status']['mode']);
            // Изменение идентификатора
            if(isset($_POST['status']['id']) AND $_POST['status']['id'] != Core::TREE_ID)
                $this->update_id($_POST['status']['id']);
            
            header("Location: /".Url::instance());
        }
        elseif(isset($_POST['tree']['activate'])){
            $this->activate();
            header("Location: /".Url::instance());
        }
        echo View::factory(Registry::i()->class.'_fetch','socket');
    }
    
    // Для режима
    private function update_mode($mode){
        $pattern = 'public static $selected_mode = Core::{mode};';
        
        $mode = Str::__($pattern, array('{mode}'=>$this->mode($mode)));
        $old_mode = Str::__($pattern, array('{mode}'=>$this->mode(Core::$selected_mode)));
        
        $dir = Core::find_file('classes_core','core');
        if(!empty($dir)){
            $found_bool = FALSE;
            $handle = fopen($dir, "r");
            while (!feof($handle)){
                $contents = fgets($handle);
                if($found_bool === FALSE AND trim($contents) == $old_mode){
                    $found_bool = TRUE;
                    $contents = str_replace($old_mode,$mode,$contents);
                }
                $all_contents .= $contents;
            }
            fclose($handle);
            file_put_contents($dir,$all_contents);
        }
    }
    
    // Для идентификатора
    private function update_id($id){
        $pattern = 'const TREE_ID = \'{id}\';';
        
        $new_id = Str::__($pattern, array('{id}'=> $id));
        $old_id = Str::__($pattern, array('{id}'=> Core::TREE_ID));

        $dir = Core::find_file('classes_core','core');
        if(!empty($dir)){
            $found_bool = FALSE;
            $handle = fopen($dir, "r");
            while (!feof($handle)){
                $contents = fgets($handle);
                if($found_bool === FALSE AND trim($contents) == $old_id){
                    $found_bool = TRUE;
                    $contents = str_replace($old_id,$new_id,$contents);
                }
                $all_contents .= $contents;
            }
            fclose($handle);
            file_put_contents($dir,$all_contents);
        }
        // Ищем папку и заменяем ее имя
        $this->folder($id);
        // Изменяет htaccess
        $this->htaccess($id);
    }
    // Преобразует число в режим
    function mode($num){
        switch($num){
            case Core::PRODUCTION:
                return "PRODUCTION";
            break;
            case Core::STAGING:
                return "STAGING";
            break;
            case Core::TESTING:
                return "TESTING";
            break;
            case Core::DEVELOPMENT:
                return "DEVELOPMENT";
            break;
        }
    }
    // Дополнительная функция для папки администратора
    private function folder($name){
        $dirs = scandir(DOCROOT);
        foreach($dirs as $dir){
            if($dir == '.' OR $dir == '..' OR $dir == 'application' OR $dir == 'media' OR $dir == 'modules' OR $dir == 'system'){
                continue;
            }
            if(is_dir($dir)){
                $folders = scandir($dir);
                if(in_array('index.php',$folders) AND in_array('system',$folders) AND in_array('modules',$folders)){
                    $admin = $dir;
                    break;
                }
            }
        }
        if(!empty($admin))
            rename(DOCROOT.$admin,DOCROOT.$name);
    }
    
    // Дополнительная функция для изменения .htaccess
    private function htaccess($admin){
        $pattern = 'RewriteRule    ^{admin}.*  {admin}/index.php/$1 [L]';
        
        $admin = Str::__($pattern, array('{admin}'=> $admin));
        
        $dir = DOCROOT.".htaccess";
        $handle = fopen($dir, "r");
        $found_bool = FALSE;
        while (!feof($handle)){
            $contents = fgets($handle);
            if($found_bool === FALSE AND trim($contents) == "# FOR ADMIN PANEL"){
                $all_contents .= $contents;
                while (!feof($handle)){
                    $contents = fgets($handle);
                    if(strstr($contents,"RewriteRule")){
                        $contents = $admin . "\r\n";
                        break;
                    }
                    $all_contents .= $contents;
                }
            }
            $all_contents .= $contents;
        }
        fclose($handle);
        file_put_contents($dir,$all_contents);
    }// Дополнительная функция для изменения .htaccess
    private function activate(){
        $pattern = 'const TREE_HOST = \'{host}\';';
        
        $host = str_replace('www.','',$_SERVER['HTTP_HOST']);
        
        $new_host = Str::__($pattern, array('{host}'=>$host));
        $old_host = Str::__($pattern, array('{host}'=>Core::TREE_HOST));
        $dir = Core::find_file('classes_core','core');
        if(!empty($dir)){
            $found_bool = FALSE;
            $handle = fopen($dir, "r");
            while (!feof($handle)){
                $contents = fgets($handle);
                if($found_bool === FALSE AND trim($contents) == $old_host){
                    $found_bool = TRUE;
                    $contents = str_replace($old_host,$new_host,$contents);
                }
                $all_contents .= $contents;
            }
            fclose($handle);
            file_put_contents($dir,$all_contents);
        }
    }
}