<?php

class Model_Error_Xml_Socket{
    public $errors;
    public $error_xml_file;
    public $dom;
    function __construct(){
        $this->error_xml_file = Core::find_file(Core_Exception_Production::$directory_error_xml,'error','xml');
        if(!empty($this->error_xml_file)){
            $this->dom = new DOMDocument(); 
            $this->dom->load($this->error_xml_file);
            // Берем узлы ошибок
            $errors = $this->dom->getElementsByTagName('error');
            if(!empty($errors))
                foreach($errors as $error){
                    if($error->nodeType == 1){
                        $id = $error->getAttribute('id');
                        $childs = $error->childNodes;
                        if(!empty($childs))
                            foreach($childs as $child){
                                if($child->nodeType == 1){
                                    $this->errors[$id][$child->nodeName] = $child->nodeValue;
                                }
                            }
                    }
                }
        }
    }
    
    function fetch(){
        if($_POST['drop']){
            $root = $this->dom->documentElement;
            $errors = $this->dom->getElementsByTagName('error');
            foreach($errors as $error){
                if($id = $error->getAttribute('id') == $_POST['drop']){
                    $element = $error;
                    break;
                }
            }
            if(!empty($element)){
                $root->removeChild($element);
                $this->dom->save($this->error_xml_file);
                unset($this->errors);
                $this->__construct();
            }
        }
        echo View::factory('error_xml_xml','socket',array('errors'=>$this->errors));
    }
    
}