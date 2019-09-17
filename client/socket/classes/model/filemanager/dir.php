<?php

class Model_Filemanager_Dir_Socket{

    public $dir;
    
    function __construct(){
        $this->dir = (Request::get('dir'))? Request::get('dir') : '.';
        if(Core::$is_windows){
            $this->dir = (mb_detect_encoding($this->dir) != 'UTF-8')? iconv('cp1251','UTF-8',$this->dir):$this->dir;
        }
        Registry::i()->file = array();
        Registry::i()->directory = array();
        if(Core::$is_windows)
            mb_detect_order("UTF-8, eucjp-win,sjis-win");
    }
    
    // Для отображения страници файлов и директорий
    function scan(){
        $direct = dir($this->dir);
        // Сохраняем путь к папке
        $path = $direct->path;
        
        // Отбераем каталоги и файлы
        Registry::i()->size = array();
        while (false !== ($entry = $direct->read())){
            if($entry == '.' OR $entry == '..')
                continue;
            
            if(is_file($direct->path.'/'.$entry)){
                    $file = $entry;
                    if(Core::$is_windows){
                        Registry::i()->file[] = (mb_detect_encoding($entry) != 'UTF-8')? iconv('cp1251','UTF-8',$entry):$entry;
                    }else{
                        Registry::i()->file[] = $entry;
                    }
                    Registry::i()->size[end(Registry::i()->file)] = $this->FBytes(filesize($path.'/'.$file));
            }else{
                if(Core::$is_windows){
                    Registry::i()->directory[] = (mb_detect_encoding($entry) != 'UTF-8')? iconv('cp1251','UTF-8',$entry):$entry;
                }else{
                    Registry::i()->directory[] = $entry;
                }
            }
        }
        asort(Registry::i()->file);
        asort(Registry::i()->directory);
        // Для кнопки назад
        $back = explode('/',$path);
        if(!empty($back) AND is_array($back)){
            array_pop($back);
            $back = implode('/',$back);
            if(empty($back))
                $back = NULL;
        }else{
            $back = NULL;
        }
        $direct->close();
        
        // Массив для передачи в шаблон
        $fond = array(
            'directories'=>Registry::i()->directory,
            'files'=>Registry::i()->file,
            'path'=>$path,
            'back'=>$back
        );
        
        echo View::factory('filemanager_dir_scan','socket',$fond);
    }
    
    // Вставка фала или директории
    function past(){
        $newDir = $this->dir.'/';
        $oldDir = key($_SESSION['directory']['save']).'/';
        foreach($_SESSION['directory']['save'] as $save){
            $save = reset($save);
            if(is_file($oldDir.$save))
                $this->past_file($oldDir.$save,$newDir.$save);
            else{
                $scan = $this->deep_scan($oldDir.$save,$newDir.$save);
                $this->past_dir($scan);
            }
        }
        unset($_SESSION['directory']['save']);
    }
    
    // Переименовать файл или директорию
    function rename(){
        $old = Request::post('old');
        $new = Request::post('new');
        $dir = Request::get('dir').'/';
        // Кодировка для windows
        if(Core::$is_windows){
            $old = (mb_detect_encoding($old) == 'UTF-8')? iconv('UTF-8','cp1251',$old):$old;
            $new = (mb_detect_encoding($new) == 'UTF-8')? iconv('UTF-8','cp1251',$new):$new;
            $dir = (mb_detect_encoding($dir) == 'UTF-8')? iconv('UTF-8','cp1251',$dir):$dir;
        }
        rename($dir.$old,$dir.$new);
    }
    // Создать файл
    function create_file(){
        $file = Request::post('create');
        $dir = Request::get('dir').'/';
        fopen($dir.$file,'a');
    }
    // Создать директорию
    function create_dir(){
        $direcorty = Request::post('create');
        $dir = Request::get('dir').'/';
        mkdir($dir.$direcorty);
    }
    
    // Архиватор
    function archivator(){
            set_time_limit(0);
            include Module::file_path('pclzip.lib','media_filemanager_archivator','socket');
            
            $lookup = array("{time}"=>date("Y.m.d"));
            
            $files_dir = key($_POST['selected']);
            $name_arch_input = Str::__(Request::post('archiv_name'),$lookup);

            $files_to_arch = array();
            $chdir = getcwd();
            chdir($files_dir);
            
            foreach(reset($_POST['selected']) as $select){
                // Кодировка для windows
                if(Core::$is_windows){
                    $select = (mb_detect_encoding($select) == 'UTF-8')? iconv('UTF-8','cp1251',$select):$select;
                }
                if(empty($name_arch_input) AND empty($name_arch))
                    $name_arch = $select;
                if(is_file($select)){
                    $files_to_arch[$select] = $select;
                }else{
                    $this->deep_scan($select,$select,$files_to_arch,TRUE);
                }
            }
            // Создаем имя
            if(!empty($name_arch)){
                $path = pathinfo($name_arch);
                $name_arch = $path['filename'].'.'.'zip';
            }else{
                $name_arch = $name_arch_input .'.'.'zip';
            }
            $archive = new PclZip($name_arch);
            $v_list = $archive->create(implode(',', $files_to_arch));
            
            chdir($chdir);
            if($v_list == 0){
               exit("Error : ".$archive->errorInfo(true));
            }
    }
    // Деархиватор
    function de_archivator(){
        set_time_limit(0);
        include Module::file_path('pclzip.lib','media_filemanager_archivator','socket');
        
        $files_dir = key($_POST['selected']);
        
        $chdir = getcwd();
        chdir($files_dir);
        
        foreach(reset($_POST['selected']) as $select){
            $archive = new PclZip($select);
            if(($list = $archive->listContent()) == 0) {
                echo "<p>Error : ".$archive->errorInfo(true)."</p>";
                continue;
            }
            $archive->extract();
        }
        
        chdir($chdir);
        
    }
    // при большом массиве
    function big_zip(){
        $extension = 'tmp';
        $file_name = $_POST['big_zip']['file'];
        
        if(!$dir = Request::get('dir')){
            return;
        }
        
        if(isset($_POST['big_zip']['end'])){
            $extension_old = $extension;
            $extension = $_POST['big_zip']['end'];
            $old = $dir.'/'.$file_name.'.'.$extension_old;
            $new = $dir.'/'.$file_name.'.'.$extension;
            // Кодировка для windows
            /*if(Core::$is_windows){
                $old = (mb_detect_encoding($old) == 'UTF-8')? iconv('UTF-8','cp1251',$old):$old;
                $new = (mb_detect_encoding($new) == 'UTF-8')? iconv('UTF-8','cp1251',$new):$new;
            }*/
            rename($old,$new);
            return;
        }
        $file = $dir.'/'.$file_name.'.'.$extension;
        // Кодировка для windows
        /*if(Core::$is_windows){
            $file = (mb_detect_encoding($file) == 'UTF-8')? iconv('UTF-8','cp1251',$file):$file;
        }*/
        
        if($_POST['big_zip']['content']){
            $handle = fopen($file, 'a');
            fwrite($handle, $_POST['big_zip']['content']);
        }
        exit();
    }
    
    // Простая проверка и вставка файла
    private function past_file($old,$new){
        if(is_file($old))
            copy($old,$new);
    }
    
    // Вставляет директорию на основе скана
    private function past_dir($dirs){
        foreach($dirs as $old => $new){
            if(is_file($old)){
                $this->past_file($old,$new);
            }else{
                if(!is_dir($new)){
                    mkdir($new);
                }
            }
        }
    }
    
    // Удаляет все файлы и саму директорию 
    function unlink(){
        $dir = key($_POST['selected']).'/';
        /*if(Core::$is_windows){
            $dir = (mb_detect_encoding($dir) == 'UTF-8')? iconv('UTF-8','cp1251',$dir):$dir;
        }*/
        foreach(reset($_POST['selected']) as $unlink){
            $unlink = $dir.$unlink;
            // Кодировка для windows
            if(Core::$is_windows){
                $unlink = (mb_detect_encoding($unlink) == 'UTF-8')? iconv('UTF-8','cp1251',$unlink):$unlink;
            }
            if(is_file($unlink)){
                unlink($unlink);
            }else{
                $this->unlink_directory($unlink);
            }
        }
    }
    
    // Для удаления файлов и директорий внутри директории
    private function unlink_directory($dir){
        // Кодировка для windows
        /*if(Core::$is_windows){
            $dir = (mb_detect_encoding($dir) != 'UTF-8')? iconv('cp1251','UTF-8',$dir):$dir;
        }*/
        if ($objs = glob($dir."/*")){
            foreach($objs as $obj) {
                is_dir($obj) ? $this->unlink_directory($obj) : unlink($obj);
            }
        }
        rmdir($dir);

    }
    
    // Вырезает из одного места и вставляет в другое
    function cut(){
        $newDir = $this->dir.'/';
        $oldDir = key($_SESSION['directory']['save']).'/';
        foreach($_SESSION['directory']['save'] as $save){
            $save = reset($save);    
            rename($oldDir.$save,$newDir.$save);
        }
        unset($_SESSION['directory']['save']);
    }
    
    // Загружает файл из строки
    function upload(){
        $uploads_dir = $this->dir;
        $files = $_POST['_FILES']['file'];
        unset($_POST['_FILES']['file']);
        foreach($files as $name => $file){
            file_put_contents($uploads_dir.'/'.$name,$file);
        }

    }
    
    // Для глубокого скана директорий
    private function deep_scan($old,$new,&$fonds = NULL,$no_root = FALSE){
        // Кодировка для windows
        /*if(Core::$is_windows){
            $old = (mb_detect_encoding($old) == 'UTF-8')? iconv('UTF-8','cp1251',$old):$old;
            $new = (mb_detect_encoding($new) == 'UTF-8')? iconv('UTF-8','cp1251',$new):$new;
        }*/
        if(empty($fonds))
            $fonds = array();
        if($no_root === FALSE)
            $fonds[$old] = $new;

        $scans = scandir($old);
        
        foreach($scans as $fond){
            // Кодировка для windows
            /*if(Core::$is_windows){
                $fond = (mb_detect_encoding($fond) == 'UTF-8')? iconv('UTF-8','cp1251',$fond):$fond;
            }*/
            if($fond == '.' OR $fond == '..')
                continue;
            if(is_file($old.'/'.$fond)){
                $fonds[$old.'/'.$fond] = $new.'/'.$fond;
            }else{
                if($no_root === FALSE)
                    $fonds[$old.'/'.$fond] = $new.'/'.$fond;
                $fonds += $this->deep_scan($old.'/'.$fond,$new.'/'.$fond,$fonds,$no_root);
            }
        }
        return $fonds;
    }
    
    // Переводит байты в мегабайты
    /*
     * @param int/string количество байт
     * @param int сколько цифр после запятой
     * @return преобразованое значение вплоть до терабайта
     */
    function FBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes?log($bytes):0)/log(1024));
        //echo $pow.'<br>';
        $pow = min($pow, count($units)-1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision).' '.$units[$pow];
    }
}