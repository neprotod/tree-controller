<?php

/*
    -----------------------------------------------------------------
    
*/

// Загружаем ядро
//if(is_file(SYSPATH.'classes\core\core'.EXT))
require SYSPATH.'classes/core/Core'.EXT;

// Устанавливаем временную зону
date_default_timezone_set('Europe/Kiev');

// Установка локали
setlocale(LC_ALL, 'ru_RU.utf-8');

/*Работа с auto-load*/
spl_autoload_register(array('Core', 'auto_load'));

// Включаем афтолоад для unserialization
//ini_set('unserialize_callback_func', 'spl_autoload_call');

/*
 * Переключалка языка
 */
 
 /******************/
 
/*
 * Описание методов и функций для инициализации
 */
Core::init();
/*
 * Запускаем пути подключения путей
 */
Core::$config->attach(new Config_File);

/*
 * Стандартные модули
 */
/**/
/*
Module::module_path(array(
    'database', 
    'arrays',
    'codebench',
    'fileManager',
));*/
Module::module_path(TRUE);
