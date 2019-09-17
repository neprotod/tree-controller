<?php
// Клиентское приложение
class Main_Module implements I_Module{
    public $socket;
    public $clients;
    function __construct(){}
    
    function index($setting = null){}
    
    function fetch(){
        if(isset($_GET['client'])){
            if(!empty($_POST['client'])){
                $this->set_client($_POST['client']);
            }
            $clients = $this->get_client();
            $content = Template::factory('','content_client-panel',array('clients'=>$clients));
        }
        else{
            if(!empty($_POST['update-client']) AND !empty($_POST['id'])){
                $this->update_client($_POST['update'],$_POST['id']);
            }
            $clients = $this->get_client();
            $content = Template::factory('','content_client',array('clients'=>$clients));
        }
        echo Template::factory('','index',array('content'=>$content));
    }
    
    function get_client(){
        $sql = "SELECT id, identifier, name, company, project_name, description, host, email, position, visible 
                FROM __client 
                ORDER BY identifier DESC";
        $sql = DB::placehold($sql);

        $query = DB::query(Database::SELECT, $sql);
        return $query->execute();
    }
    
    function set_client($clients){
        $sql = "INSERT INTO __client SET ";
        foreach($clients as $key => $client){
            $sql .= "{$key}=".DB::escape($client).',';
        }
        $sql = rtrim($sql,',');

        $sql = DB::placehold($sql);

        $query = DB::query(Database::INSERT, $sql);
        Registry::i()->massage = "Клиент был добавлен";
        return $query->execute();
    }
    
    function update_client($clients,$id){
        $sql = "UPDATE __client SET ";
        foreach($clients as $key => $client){
            $sql .= "{$key}=".DB::escape($client).',';
        }
        /*echo $sql;
        exit();*/
        $sql = rtrim($sql,',');
        $sql .= " WHERE id=$id";
        $sql = DB::placehold($sql);

        $query = DB::query(Database::UPDATE, $sql);
        Registry::i()->massage = "Клиент был обнавлен";
        return $query->execute();
    }

}