<?php


namespace Okay\Core;


/**
 * Класс-обертка для конфигурационного файла с настройками магазина
 * В отличие от класса Settings, Config оперирует низкоуровневыми настройками, например найстройками базы данных.
 */
class Config
{

    /*Версия системы*/
    public $version = '4.0.2';
    /*Тип системы*/
    public $version_type = 'pro';
    
    /*Файл для хранения настроек*/
    public $configFile;
    public $configLocalFile;

    public $salt;
    private $vars = [];
    private $masterVars = [];
    private $localVars = [];

    public function __construct($configFile, $configLocalFile)
    {
        $this->configFile = $configFile;
        $this->configLocalFile = $configLocalFile;
        $this->initConfig();
    }

    /**
     * @param $name
     * @return mixed|null
     * @throws \Exception
     * Выбор конфига
     */
    public function get($name)
    {
        if ($name == 'root_url') {
            throw new \Exception('Config::root_url is remove. Use Request::getRootUrl()');
        }

        if ($name == 'subfolder') {
            throw new \Exception('Config::subfolder is remove. Use Request::getSubDir()');
        }

        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @param $value
     * Запись данных в конфиг
     */
    public function set($name, $value)
    {
        if (!isset($this->vars[$name]) && !isset($this->localVars[$name])) {
            return;
        }

        // Определяем в каком файле конфига переопределять значения
        if (isset($this->localVars[$name])) {
            $configFile = $this->configLocalFile;
        } else {
            $configFile = $this->configFile;
        }

        $conf = file_get_contents($configFile);
        $conf = preg_replace("/".$name."\s*=.*\n/i", $name.' = '.$value."\r\n", $conf);
        $cf = fopen($configFile, 'w');
        fwrite($cf, $conf);
        fclose($cf);
        $this->vars[$name] = $value;
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /*Формирование токена*/
    public function token($text)
    {
        return md5($text.$this->salt);
    }

    /*Проверка токена*/
    public function checkToken($text, $token)
    {
        if(!empty($token) && $token === $this->token($text)) {
            return true;
        }
        return false;
    }
    
    private function initConfig()
    {
        /*Читаем настройки из дефолтного файла*/
        $ini = parse_ini_file($this->configFile);
        /*Записываем настройку как переменную класса*/
        foreach ($ini as $var=>$value) {
            $this->masterVars[$var] = $value;
            $this->vars[$var] = $value;
        }

        /*Заменяем настройки, если есть локальный конфиг*/
        if (file_exists($this->configLocalFile)) {
            $ini = parse_ini_file($this->configLocalFile);
            foreach ($ini as $var => $value) {
                $this->localVars[$var] = $this->vars[$var] = $value;
            }
        }

        // Вычисляем DOCUMENT_ROOT вручную, так как иногда в нем находится что-то левое
        $localPath = getenv("SCRIPT_NAME");
        $absolutePath = getenv("SCRIPT_FILENAME");
        $_SERVER['DOCUMENT_ROOT'] = substr($absolutePath,0, strpos($absolutePath, $localPath));

        // Определяем корневую директорию сайта
        $this->vars['root_dir'] =  dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR;

        // Максимальный размер загружаемых файлов
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $this->vars['max_upload_filesize'] = min($max_upload, $max_post, $memory_limit)*1024*1024;

        // Соль (разная для каждой копии сайта, изменяющаяся при изменении config-файла)
        $s = stat($this->configFile);
        $this->vars['salt'] = md5(md5_file($this->configFile).$s['dev'].$s['ino'].$s['uid'].$s['mtime']);

        // Часовой пояс
        if (!empty($this->vars['php_timezone'])) {
            date_default_timezone_set($this->vars['php_timezone']);
        }
    }

    public function loadConfigsFrom($filename)
    {
        if (!is_file($filename)) {
            throw new \Exception("Cannot load configs from \"{$filename}\"");
        }

        $ini = parse_ini_file($filename);
        foreach ($ini as $var => $value) {
            if (isset($this->masterVars[$var])) {
                throw new \Exception("Duplicate parameter \"{$var}\"");
            }

            if (!isset($this->localVars[$var])) {
                $this->vars[$var] = $value;
            }
        }
    }
    
}
