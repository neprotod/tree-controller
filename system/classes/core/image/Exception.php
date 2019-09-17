<?php

class Core_Image_Exception extends Exception {
    public function __construct($message, array $variables = NULL, $code = 0){

        return realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'error.png';
    }
}