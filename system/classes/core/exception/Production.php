<?php
/**
 * Tree exception class. Translates exceptions using the [I18n] class.
 *
 * @package    Tree
 * @category   Exceptions
 */
class Core_Exception_Production extends Exception {
    
    public $massage;
    public $code;
    public static $php_errors = array(
        E_ERROR              => 'Fatal Error',
        E_USER_ERROR         => 'User Error',
        E_PARSE              => 'Parse Error',
        E_WARNING            => 'Warning',
        E_USER_WARNING       => 'User Warning',
        E_STRICT             => 'Strict',
        E_NOTICE             => 'Notice',
        E_RECOVERABLE_ERROR  => 'Recoverable Error',
    );
    
    static function handler(Exception $e){
        if ($e instanceof ErrorException){
            @header("Cache-control: no-store,max-age=0");
            $e->massage = '<p>Приносим извинения, на сайте произашла ошибка.</p><p>Администрация оповещина и решит ее в скором времени</p>';
        }
        if(!isset($e->massage))
            echo "<p>Приносим свои изменения, на сайте произашла незначительная ошибка.</p> <a href='/'>Другие страници должны быть доступны.</a>";
        else
            echo $e->massage;
        exit();
    }
    
    public function __construct($massage, $code = NULL){
            $this->massage = $massage;
            //parent::__construct($massage, $code);
    }
}
