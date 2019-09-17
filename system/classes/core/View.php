<?php
/**
 * Действует как обертки объекта для HTML страниц со встроенным PHP.
 *
 * @package    Tree
 * @category   Base
 */
class Core_View {

    // Массив глобальных переменных
    protected static $_global_data = array();

    
    // Просмотр файла
    protected $_file;

    // Массив локальных переменных
    protected $_data = array();
    
    /*********methods***********/
    
    
    /**
     * Возвращает новый объект Вида. Если вы не определяете параметр "Файл",
     * вам необходимо вызвать [View::set_filename].
     *
     *     $view = View::factory($file);
     *
     * @param   string  view filename
     * @param   array   array of values
     * @return  View
     */
     
    
    public static function factory($file = NULL, $module = NULL, array $data = NULL){
        $view = new View($file, $module, $data);
        
        try{
            return $view->render();
        }catch (Exception $e){
            // Отображение сообщение об исключении
            Core_Exception::handler($e);

            return '';
        }
    }

    /**
     * Захватывает вывод, создаваемый при представлении.
     * Чтобы сделать локальные переменные будут извлечены данные представления.  
     * Этот метод является статическим для предотвращения разрешение объекта 
     * области.
     *
     *     $output = View::capture($file, $data);
     *
     * @param   string  filename
     * @param   array   variables
     * @return  string
     */
    protected static function capture($core_view_filename, array $core_view_data)
    {
        // Импорт представления переменных в локальное пространство имен
        extract($core_view_data, EXTR_SKIP);

        if (View::$_global_data){
            // Импортировать глобальные переменные в локальное пространство имен
            extract(View::$_global_data, EXTR_SKIP);
        }

        // Захват выходные данные вида
        ob_start();

        try{
            // Загрузить представления в текущей области
            include $core_view_filename;
        }
        catch (Exception $e){
            // Удалить выходной буфер
            ob_end_clean();

            // Повторно вызвать исключение
            throw $e;
        }

        // Получить захваченные выходные данные и закрыть буфер
        return ob_get_clean();
    }

    /**
     * Устанавливает глобальную переменную, похож на [View::set], 
     * за исключением того, что переменная будет доступна для всех 
     * представлений.
     * 
     *
     *     View::set_global($name, $value);
     *
     * @param   string  имя переменной или массив переменных
     * @param   mixed   value
     * @return  void
     */
    public static function set_global($key, $value = NULL){
        if (is_array($key)){
            foreach ($key as $key2 => $value){
                View::$_global_data[$key2] = $value;
            }
        }else{
            View::$_global_data[$key] = $value;
        }
    }

    /**
     * Присваивает глобальную переменную по ссылке, похож на [View::bind], 
     * за исключением что переменная будет доступна для всех представлений.
     *
     *     View::bind_global($key, $value);
     *
     * @param   string  имя переменной
     * @param   mixed   ссылаемая переменная
     * @return  void
     */
    public static function bind_global($key, & $value){
        View::$_global_data[$key] =& $value;
    }


    /**
     * Установка начальной имя файла вида и локальные данные.
     * Файл почти всегда должен загружатся с помощью [View::factory].
     *
     *     $view = new View($file);
     *
     * @param   string  view filename
     * @param   array   array of values
     * @return  void
     * @uses    View::set_filename
     */
    public function __construct($file = NULL, $module, array $data = NULL){
        if ($file !== NULL AND !is_object($file)){
            $this->set_filename($file, $module);
        }else{
            if($file instanceof Template)
                $this->_file = $file->template();
        }

        if ($data !== NULL){
            // Добавьте значения в текущих данных
            $this->_data = $data + $this->_data;
        }
    }

    /**
     * Магический метод, ищет данную переменную и возвращает ее значение.
     * Локальные переменные будут возвращены до глобальных переменных.
     *
     *     $value = $view->foo;
     *
     * [!!] Если переменная еще не определены, будет вызвано исключение.
     *
     * @param   string  variable name
     * @return  mixed
     * @throws  Kohana_Exception
     */
    public function & __get($key){
        if (array_key_exists($key, $this->_data)){
            return $this->_data[$key];
        }
        elseif (array_key_exists($key, View::$_global_data)){
            return View::$_global_data[$key];
        }
        else{
            throw new Core_Exception('Переменная вида не установлена: '.$key.'');
        }
    }

    /**
     * Магический метод, вызов [View::set] для загрузки переменных.
     *
     *     $view->foo = 'something';
     *
     * @param   string  variable name
     * @param   mixed   value
     * @return  void
     */
    public function __set($key, $value){
        $this->set($key, $value);
    }

    /**
     * Магический метод, определяет, установлена ли переменная.
     *
     *     isset($view->foo);
     *
     *
     * @param   string  variable name
     * @return  boolean
     */
    public function __isset($key){
        return (isset($this->_data[$key]) OR isset(View::$_global_data[$key]));
    }

    /**
     * Волшебный метод, сбрасывает переменную.
     *
     *     unset($view->foo);
     *
     * @param   string  variable name
     * @return  void
     */
    public function __unset($key){
        unset($this->_data[$key], View::$_global_data[$key]);
    }

    /**
     * Волшебный метод, возвращает выходные данные [View::render].
     *
     * @return  string
     * @uses    View::render
     */
    /*public function __toString(){
        try{
            return $this->render();
        }catch (Exception $e){
            // Отображение сообщение об исключении
            Core_Exception::handler($e);

            return '';
        }
    }*/

    /**
     * Устанавливает имя файла вида.
     *
     *     $view->set_filename($file);
     *
     * @param   string  view filename
     * @return  View
     * @throws  Core_Exception
     */
    function set_filename($file, $module){
        $file = 'views_'.$file;
        
        // создаем путь к файлу
        $file = str_replace('_', DIRECTORY_SEPARATOR, strtolower($file));
        
        
        // Абсолютный путь к файлу
        if(isset($module))
            $path = Module::mod_path($module).$file.EXT;
        else 
            $path = $file.EXT;
        // Храните путь к файлу локально
        $this->_file = $path;

        return $this;
    }

    /**
     * Присваивает переменную по имени. Заданные значения будут доступны как
     * переменная в файле вида:
     *
     *     // Это значение можно получить доступ как $foo в виде.
     *     $view->set('foo', 'my value');
     *
     * Можно также использовать массив чтобы сразу задать несколько значений:
     *
     *     // Создает значение $food и $beverage в виде
     *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
     *
     * @param   string   variable name or an array of variables
     * @param   mixed    value
     * @return  $this
     */
    function set($key, $value = NULL){
        if (is_array($key)){
            foreach ($key as $name => $value){
                $this->_data[$name] = $value;
            }
        }else{
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Назначает значение путем ссылки. Преимущество связывания в том, что 
     * значения могут быть изменены без их повторной установки. Это также 
     * возможно для привязки переменных прежде, чем будет иметь значение. 
     * Заданные значения будут доступны как переменная в файле вида :
     *
     *     // Эта ссылка может быть доступна как $ref в пределах вида
     *     $view->bind('ref', $bar);
     *
     * @param   string   variable name
     * @param   mixed    referenced variable
     * @return  $this
     */
    public function bind($key, & $value){
        $this->_data[$key] =& $value;

        return $this;
    }

    /**
     * Визуализирует представление объекта в строку. Глобальные и локальные 
     * данные объединяются и извлекаются, что бы создать переменные в пределах 
     * вида.
     *
     *     $output = $view->render();
     *
     * [!!] Глобальные переменные с тем же именем ключа как локальные 
     * переменные будут заменены локальной переменной.
     *
     * @param    string  view filename
     * @return   string
     * @throws   Core_Exception
     * @uses     View::capture
     */
    public function render($file = NULL){
        if ($file !== NULL){
            $this->set_filename($file);
        }

        if (empty($this->_file)){
            throw new Core_Exception('Необходимо установить файл для использования в пределах вашей видимости перед отрисовкой');
        }

        // Объединить локальные и глобальные данные и записи выходных данных
        return View::capture($this->_file, $this->_data);
    }

    
    /**********************/
    static function path($directory){
        $dir = explode(DOCROOT,$directory);
        $dir = str_replace('\\', '/', strtolower($dir[1]));
        return $dir;
    }
}
