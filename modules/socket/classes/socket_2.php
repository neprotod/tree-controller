<?php
// Для критической ошибки c3bc592b8a755ac3148c0e01271cf6dc
// Клиентское приложение
class Socket_Module implements I_Module{
    function __construct(){
        header("Cache-control: no-store,max-age=0");
        /*header("Expires: " . date("r"));*/
        set_time_limit(0);
        $_POST['type'] = 'tree';
        $_POST['init'] = '0000';
    }
    
    function index($setting = null){}
    
    function conection($url){
        
        if($_POST['tree']){
            $this->action($url);
        }
        $_POST['session'] = md5($_POST['type'].$_POST['init']);
        
        // Если нужно загрузить файлы на сервер
        $this->file();
        
        // Обрабатываем пост запрос
        $str_query = $this->post();
        
        try{
            echo $this->connect($url,array(),$str_query);
        }catch(Exception $e){
            unset($_POST);
            $this->__construct();
            $str_query = $this->post();
            echo $this->connect($url,array(),$str_query);
        }
    }
    
    // Для упоковывания данных
    function packSet($string){
        if(is_array($string)){
            $string = serialize($string);
        }
        if(is_string($string)){
            $bin = unpack("H*",$string);
            $bin = $bin[1];
        }
        return $bin;
    }
    
    // Обрабатывает специфические запросы
    private function action($url){
        //set_time_limit(0);
        if($_POST['tree']['get']){
            $file_name = utf8_decode($_POST['tree']['get']['file']);
            header("Content-type: file/octet-stream");
            header("Content-disposition: attachment; filename=\"{$file_name}\"");
            // Обрабатываем пост запрос
            $str_query = $this->post();
            
            
            echo $this->connect($url,array(),$str_query);
            
            exit();
        }
        if($_POST['tree']['big_zip']){
        
            //set_time_limit(0);
    
            //echo $this->connect($url,array('Zip:'=>'big_zip'),$str_query);
            if($canonical = $_POST['tree']['big_zip']['canonical'] AND $file_name = $_POST['tree']['big_zip']['file']){
                $file_name = pathinfo($file_name);
                
                $file = iconv('utf-8','cp1251',$canonical);
                $handle = @fopen($file, "r");
                if ($handle){
                    while(!feof($handle)){
                        $buffer = fread($handle, 10000000);
                        $_POST['big_zip']['content'] = $buffer;
                        $_POST['big_zip']['file'] = $file_name['filename'];
                        $str_query = $this->post();
                        echo $this->connect($url,array('Zip'=>'big_zip'),$str_query);
                    }
                    if (!feof($handle)) {
                        echo "Error: unexpected fgets() fail\n";
                    }
                    fclose($handle);
                    
                    $_POST['big_zip']['file'] = $file_name['filename'];
                    $_POST['big_zip']['end'] = $file_name['extension'];
                    $str_query = $this->post();
                    
                    echo $this->connect($url,array('Zip'=>'big_zip'),$str_query);
                }
                exit();
            }
        }
    }
    
    // Формирование сохранение файла
    private function file(){
        if(isset($_FILES)){
            $_POST['_FILES'] = array();
            foreach($_FILES as $key => $fonds){
                $files = $fonds;
                for($i = 0,$count = count($files['name']); $i < $count; $i++){
                    if ($files['error'][$i] == UPLOAD_ERR_OK){
                        $tmp_name = $files["tmp_name"][$i];
                        $name = $files["name"][$i];
                        $_POST['_FILES'][$key][$name] = file_get_contents($files["tmp_name"][$i]);
                    }
                }
            }
        }
    }
    
    // Формирование пост запроса
    private function post(){
        if(!empty($_POST)){
            $str_query = $this->packSet(serialize($_POST));
        }
        return $str_query;
    }
    
    // Основная функция соеденения сокета
    /*
     * @params string доменное имя сокетного соеденения как напрмер site.ru
     * @params array дополнительные заголовки, новые заголовки перепишут старые
     * @str_query string подготовленные POST данные
     * @return результат выполнения сервера
     */
    private function connect($url,$headers = array(),$str_query = NULL){
        if(empty($url)){
            return;
        }
        $default_header = array(
            "Content-Type"=>"application/x-www-form-urlencoded",

            "Host"=>$url,
            "User-Agent"=>"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0",
            "Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language"=>"ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Accept-Encoding"=>"gzip, deflate",
            "Connection"=>"close",
            "Cache-Control"=>"max-age=0",
            "Type"=>"socket",
            "Init"=>"0000"
        );

        
        $headers = Arr::merge($default_header,$headers);

        $out = '';
        
        if(!empty($headers))
            foreach($headers as $header => $value){
                $out .="{$header}: {$value}\r\n";
            }
        
        $query_str = $str_query;

        $str_query = "tree={$str_query}&\r\n\r\n";
        
        $out .= "Content-length: " . UTF8::strlen($str_query). "\r\n\r\n";
        $out .= $str_query;
        
        $options = array('http'=>array(
            'method' => 'POST',
            'header' => $out
        ));
        $connect = stream_context_create($options);

        return file_get_contents("https://".$url, FALSE, $connect);
        
        $ch = curl_init("https://seor.ua/");
        //$ch = curl_init("https://".$url);

        $header_curl = array();
        foreach($headers AS $key => $val){
            $header_curl[] = "$key: $val";
        }
        $header_curl[] = "Content-length: " . UTF8::strlen($str_query). "\r\n\r\n" . $str_query;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_curl);
        /*curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("tree" => $query_str));
        
        curl_setopt($ch, CURLOPT_HEADER, 1);
                
        */
        
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $p = curl_exec($ch);
        echo "<pre>";
        var_dump(curl_getinfo($ch));
        exit;
        
        //$result = curl_exec($ch);
        curl_close($ch); 
        
        return $result;
        echo "<pre>";
        var_dump($result);
        exit; 
    }

}