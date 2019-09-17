<?php
/**
 * File-based configuration reader. Multiple configuration directories can be
 * used by attaching multiple instances of this class to [Config].
 *
 * @package    Kohana
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Core_Config_File extends Config_Reader {

    /**
     * @var  string  Имя группы конфигурации
     */
    protected $_configuration_group;
    
    /**
     * @var  string  Путь к конфигурациям
     */
    public $_directory;
    
    /**
     * @var  bool  Изменилась ли группа конфигурации?
     */
    protected $_configuration_modified = FALSE;

    public function __construct($directory = 'config')
    {
        // Установить имя каталога конфигурации
        $this->_directory = trim($directory, '/');

        // Загрузите пустой массив
        parent::__construct();
    }

    /**
     * Загрузите и поглотите все конфигурационные файлы в этой группе.
     *
     *     $config->load($name);
     *
     * @param   string  имя группы конфигурации
     * @param   array   конфигурации массива
     * @return  $this   клон текущего объекта
     * @uses    Core::load
     */
    public function load($group, array $config = NULL){
        if ($files = Core::find_file($this->_directory, $group, NULL, TRUE)){
            // Инициализируйте массив config
            $config = array();

            foreach ($files as $file){
                // Слияние каждого файла конфигурации массива
                $config = Arr::merge($config, Core::load($file));
            }
        }

        return parent::load($group, $config);
    }

}
