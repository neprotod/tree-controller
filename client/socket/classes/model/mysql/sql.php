<?php

class Model_Mysql_Sql_Socket{
    public $tables;
    public $backup;
    function __construct(){
        $this->backup = Model::factory('mysql_backup','socket');
        $this->tables = $this->backup->show_table();
    }
    
    function fetch(){
        if(Request::post("drop")){
            if($table = Request::post("table"))
                $this->sql("DROP TABLE {$table}", 4);
            $this->tables = $this->backup->show_table(TRUE);
        }
        elseif(Request::post("save")){
            $table = Request::post("table");
            $key = Request::post("key");
            $this->backup->create_file(array($this->tables[$key]),array($this->tables[$key]));
            exit();
        }
        if($mysql = Request::post("mysql")){
            $result = $this->sql($mysql['sql'], $mysql['type']);
        }
        
        echo View::factory('mysql_sql_fetch','socket',array('result'=>$result,'tables'=>$this->tables,'database'=>$this->backup->db_name));
    }
    
    function sql($sql,$type){
        $sql = DB::placehold($sql);
        $type = intval($type);
        $query = DB::query($type, $sql);
        try{
            $result = $query->execute();
        }catch(Exception $e){
            $result = array(
                "Massage" => $e->getMessage(),
            );
        }
        return $result;
    }
}