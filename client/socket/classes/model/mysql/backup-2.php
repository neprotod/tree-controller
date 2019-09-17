<?php

class Model_Mysql_Backup_Socket{
    
    private $table;
    
    public $db_name;
    
    function __construct(){
        // Узнаем имя базы данных
        $config = Core::config('database')->default['connection'];
        $db_name = explode(';',$config['dsn']);
        $db_name = array_pop($db_name);
        
        $db_name = explode('=',$db_name);
        $db_name = end($db_name);
        if(!empty($db_name))
            $this->db_name = $db_name;
    }
    
    function fetch(){
        if($_POST['all']){
            $this->create_file($this->show_table());
        }
        $result = $this->show_table();
        echo View::factory('mysql_backup_fetch','socket',array('show_table'=>$result));
    }
    
    function show_table(){
        if(!empty($this->table))
            return $this->table;
        
        $sql = "SELECT table_name, table_comment
            FROM information_schema.tables
            WHERE table_schema = '{$this->db_name}'";
        $sql = DB::placehold($sql);
        
        //$sql = "SHOW TABLES";
        $query = DB::query(Database::SELECT, $sql);
        
        $this->table = $query->execute();
        
        
        return $this->table;
    }
    
    private function get_table_information($table){
        $sql = "SELECT *
                    FROM information_schema.columns
                    WHERE table_schema = '{$this->db_name}'
                    AND table_name = '{$table}'";/**/
        $sql = DB::placehold($sql);
        
        /*$sql = "DESC {$table}";*/
        $query = DB::query(Database::SELECT, $sql);
        $tables_info = $query->execute();
        
        $table_info_return = array();
        foreach($tables_info as $tables){
            $table_info_return[$tables['TABLE_NAME']][$tables['COLUMN_NAME']] = array(
                'type' => $tables['COLUMN_TYPE'],
                'null' => (strtolower($tables['IS_NULLABLE']) == 'no')?'NOT NULL':'NULL',
                'extra' => $tables['EXTRA'],
                'default' => $tables['COLUMN_DEFAULT'],
                'comment' => $tables['COLUMN_COMMENT']
            );
        }
        
        /*$sql = "SELECT *
                    FROM information_schema.statistics info
                    INNER JOIN information_schema.table_constraints constrai ON info.INDEX_NAME = constrai.CONSTRAINT_NAME
                    WHERE info.table_schema = '{$this->db_name}' 
                    AND constrai.table_schema = '{$this->db_name}'
                    AND constrai.table_name = '{$table}'
                    AND info.table_name = '{$table}'";
        $sql = DB::placehold($sql);
        
        $query = DB::query(Database::SELECT, $sql);
        $key_info = $query->execute();

        foreach($key_info as $keys){
            $table_info_return[$keys['TABLE_NAME']]['KEY'][$keys['CONSTRAINT_TYPE']][$keys['INDEX_NAME']][] = $keys['COLUMN_NAME'];
        }
        $sql = "SELECT TABLE_NAME, ENGINE, AUTO_INCREMENT, TABLE_COLLATION
                    FROM information_schema.tables
                    WHERE table_schema = '{$this->db_name}'
                    AND table_name = '{$table}'";
        $sql = DB::placehold($sql);
        
        $query = DB::query(Database::SELECT, $sql);
        $options = reset($query->execute());
        $table_info_return[$options['TABLE_NAME']]['OPTIONS'] = array(
            'engine' => $options['ENGINE'],
            'auto_increment' => $options['AUTO_INCREMENT'],
            'table_collation' => $options['TABLE_COLLATION'],
        );
        */
        var_dump($table_info_return);
        return $table_info_return;
    }
    
    private function get_insert($table){
        $sql = "SELECT *
                    FROM {$table}";
        $sql = DB::placehold($sql);
        
        $query = DB::query(Database::SELECT, $sql);
        return $query->execute();
    }
    
    function create_file($show_table){
        $sql_return = '';
        foreach($show_table as $show){
            $tables = $this->get_table_information($show['table_name']);
            
            $key_for_insert = array();
            foreach($tables as $name => $table){
                $sql_return .= "--\r\n";
                $sql_return .= "-- Структура таблицы `{$name}`\r\n";
                $sql_return .= "--\r\n";
                $sql_return .= "\r\n";
                
                $sql_return .= "CREATE TABLE IF NOT EXISTS `{$name}` (\r\n";
                foreach($table as $column_name => $value){
                    // Сохраняем ключь, он пригодится при вормирование INSERT
                    $key_for_insert[] = $column_name;
                    $default = ($value['default'] !== NULL)? "DEFAULT ": '';
                    $sql_return .= "\t`{$column_name}` {$value['type']} {$value['null']} {$value['extra']}";
                }
            }
            //$insert = $this->get_insert($show['table_name']);
            
        }
        //echo $sql_return;
    }
}