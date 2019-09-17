<?php

class Controller_Mysql_Socket{

    public $sql;
    
    function __construct(){
        if(empty(Registry::i()->urlArray)){
            Registry::i()->urlArray[] = 'sql';
        }
        $this->sql = Model::factory('mysql_sql','socket');
        $this->backup = Model::factory('mysql_backup','socket');
    }
    
    function fetch(){
        $action = reset(Registry::i()->urlArray);
        if($action == 'sql'){
            $this->sql->fetch();
        }
        elseif($action == 'backup'){
            $this->backup->fetch();
        }
    }
}