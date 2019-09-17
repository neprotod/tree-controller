<?php
// Клиентское приложение
class Soap_Module implements I_Module{
    function __construct(){
    
    }
    
    function index($setting = null){
        try {
            $wsdl = Module::file_path('stock-valid','wsdl','soap','wsdl');
            // Создание SOAP-клиента
            $client = new SoapClient($wsdl);
            
            // Посылка SOAP-запроса c получением результат
            $result = $client->getStock("2");
            echo "Текущий запас на складе: ", $result;
        } catch (SoapFault $exception) {
            echo $exception->getMessage();    
        }
    }
    
}