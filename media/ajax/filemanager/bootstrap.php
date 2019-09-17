<?php

chdir('../../..');
$application = 'application';

$modules = 'modules';

$system = 'system';


/*
 * ���������� ������
 */

define('EXT', '.php');

/*
 * ����������� ������
 */

//error_reporting(E_ALL | E_STRICT);

/*
 * ���� � ��������� ��������
 */
define('DOCROOT', realpath(getcwd()).DIRECTORY_SEPARATOR);

/*
 * ��������� ���� ����������� ��������� ��������
 */
 
if (!is_dir($application) AND is_dir(DOCROOT.$application))
    $application = DOCROOT.$application;

if (!is_dir($modules) AND is_dir(DOCROOT.$modules))
    $modules = DOCROOT.$modules;

if (!is_dir($system) AND is_dir(DOCROOT.$system))
    $system = DOCROOT.$system;

/*
 * ������� ���������� ����
 */
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);

// ������� ������
unset($application, $modules, $system);

require SYSPATH.'classes/core/Core'.EXT;

// ������������� ��������� ����
date_default_timezone_set('Europe/Kiev');

// ��������� ������
setlocale(LC_ALL, 'ru_RU.utf-8');

/*������ � auto-load*/
spl_autoload_register(array('Core', 'auto_load'));

// �������� �������� ��� unserialization
//ini_set('unserialize_callback_func', 'spl_autoload_call');

/*
 * ������������ �����
 */
 
 /******************/
 
/*
 * �������� ������� � ������� ��� �������������
 */
Core::init();
/*
 * ��������� ���� ����������� �����
 */
Core::$config->attach(new Config_File);

/*
 * ����������� ������
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
