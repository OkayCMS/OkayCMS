<?php


namespace Okay\Core;


class Request
{
    
    private $langId;
    private $startTime;

    /**
     * @var string содержит название директории в которой установлен окай.
     */
    private $basePath;

    /**
     * @var string содержит URL страницы, без папки и языковой приставки
     */
    private $pageUrl;
    
    private static $domain;
    private static $protocol;
    private static $subDir;
    
    public function __construct()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            $this->setBasePath($_SERVER['REQUEST_URI']);
        }

        if ($this->get('lang_id', 'integer')) {
            $this->langId = $this->get('lang_id', 'integer');
        }
        
    }

    /**
     * Возвращает массив $argv.
     * Если параметры были переданы как key=value key2=value2 будет возвращен массив
     * где ключ будет названием параметра
     * @return array
     */
    public static function getArgv()
    {
        global $argv;
        $result = [];
        if (!empty($argv)) {
            for ($i = 1; $i < count($argv); $i++) {
                $arg = explode("=", $argv[$i]);
                if (count($arg) == 2) {
                    $result[trim($arg[0])] = trim($arg[1]);
                } else {
                    $result[] = trim($argv[$i]);
                }
            }
        }
        return $result;
    }

    /**
     * Метод возвращает текущий URL с протоколом и REQUEST_URI
     * 
     * @return string
     */
    public static function getCurrentUrl()
    {
        return self::getDomainWithProtocol() . $_SERVER['REQUEST_URI'];
    }

    /**
     * Return the query url, without QUERY_STRING
     * 
     * @return string
     */
    public static function getCurrentQueryPath()
    {
        return self::getDomainWithProtocol() . rtrim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '?');
    }

    /**
     * Return current QUERY_STRING
     * 
     * @return string
     */
    public static function getCurrentQueryString() : string
    {
        return (!empty($_SERVER['QUERY_STRING']) ? '?' : '') . $_SERVER['QUERY_STRING'];
    }
    
    public function getStartTime()
    {
        if (empty($this->startTime)) {
            throw new \Exception('Start time not been setted');
        }
        return $this->startTime;
    }
    
    public function setStartTime($time)
    {
        $this->startTime = (float)$time;
    }
    
    public function getBasePathWithDomain()
    {
        return self::getProtocol() . '://' . self::getDomain() . $this->getBasePath();
    }

    /**
     * Метод возвращает REQUEST_URI без учёта подпапки сайта. т.е. только от корня сайта
     * Напр. для URL https://demookay.com/subfolder/catalog/mebel-dlya-doma?param=value
     * $_SERVER['REQUEST_URI'] будет равен /subfolder/catalog/mebel-dlya-doma?param=value
     * а текущий метод вернёт catalog/mebel-dlya-doma?param=value
     * 
     * @return string
     */
    public static function getRequestUri()
    {
        return ltrim(str_replace(self::getRootUrl(), '', self::getCurrentUrl()), '/');
    }
    
    /**
     * Метод возвращает домен вместе с подпапкой (корень сайта)
     * 
     * @return string
     */
    public static function getRootUrl()
    {
        return self::getDomainWithProtocol() . self::getSubDir();
    }

    /**
     * Метод возвращает текущий домен с протоколом
     * 
     * @return string
     */
    public static function getDomainWithProtocol()
    {
        return self::getProtocol() . '://' . self::getDomain();
    }
    
    public function getBasePath()
    {
        return $this->basePath;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function getPageUrl()
    {
        return $this->pageUrl;
    }

    public function setPageUrl($pageUrl)
    {
        $this->pageUrl = $pageUrl;
    }

    public static function getReferer()
    {
        return !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }
    
    /**
     * Определение request-метода обращения к странице (GET, POST)
     * Если задан аргумент функции (название метода, в любом регистре), возвращает true или false
     * Если аргумент не задан, возвращает имя метода
     * Пример:
     *
     *    if($request->method('post'))
     *        print 'Request method is POST';
     * @var string $method
     * @return string
     */
    public function method($method = null)
    {
        if (!empty($method)) {
            return strtolower($_SERVER['REQUEST_METHOD']) == strtolower($method);
        }
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Возвращает переменную _GET, отфильтрованную по заданному типу, если во втором параметре указан тип фильтра
     * Второй параметр $type может иметь такие значения: integer, string, boolean
     * Если $type не задан, возвращает переменную в чистом виде
     * @var string $name
     * @var string $type
     * @var string $default
     * @var bool $stripTags
     * @return mixed
     */
    public function get($name, $type = null, $default = null, $stripTags = true)
    {
        $val = null;
        if (isset($_GET[$name])) {
            $val = $_GET[$name];
        }
        
        if (!empty($type) && is_array($val)) {
            $val = reset($val);
        }

        if (empty($val) && $default !== null) {
            $val = $default;
        }

        // На входе удаляем html теги
        if ($stripTags === true && !empty($val)) {
            $val = $this->recursiveStripTags($val);
        }
        
        if ($type == 'string') {
            return strval(preg_replace('/[^\p{L}\p{Nd}\d\s_\-.%]/ui', '', $val));
        }
        
        if ($type == 'integer' || $type == 'int') {
            return intval($val);
        }
        
        if ($type == 'float') {
            return floatval($val);
        }
        
        if ($type == 'boolean' || $type == 'bool') {
            return !empty($val);
        }
        
        return $val;
    }
    
    private function recursiveStripTags($val)
    {
        if (is_array($val) || is_object($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->recursiveStripTags($v);
            }
            return $val;
        }
        
        return htmlspecialchars(strip_tags($val));
    }
    
    /**
     * Возвращает переменную _POST, отфильтрованную по заданному типу, если во втором параметре указан тип фильтра
     * Второй параметр $type может иметь такие значения: integer, string, boolean
     * Если $type не задан, возвращает переменную в чистом виде
     * @var string $name
     * @var string $type
     * @var string $default
     * @return mixed
     */
    public function post($name = null, $type = null, $default = null) {
        $val = null;
        if (!empty($name) && isset($_POST[$name])) {
            $val = $_POST[$name];
        } elseif (empty($name)) {
            $val = file_get_contents('php://input');
        }

        if (empty($val) && $default !== null) {
            $val = $default;
        }

        if ($type == 'string') {
            return strval(preg_replace('/[^\p{L}\p{Nd}\d\s_\-.%]/ui', '', $val));
        }
        
        if ($type == 'integer' || $type == 'int') {
            return intval($val);
        }
        
        if ($type == 'float') {
            return floatval($val);
        }
        
        if ($type == 'boolean' || $type == 'bool') {
            return !empty($val);
        }
        
        return $val;
    }
    
    /**
     * Возвращает переменную _FILES
     * Обычно переменные _FILES являются двухмерными массивами, поэтому можно указать второй параметр,
     * например, чтобы получить имя загруженного файла: $filename = $request->files('myfile', 'name');
     * @var string $name
     * @var string $name2
     * @return array|null
     */
    public function files($name, $name2 = null)
    {
        if (!empty($name2) && !empty($_FILES[$name][$name2])) {
            return $_FILES[$name][$name2];
        } elseif (empty($name2) && !empty($_FILES[$name])) {
            return $_FILES[$name];
        }

        return null;
    }

    public static function getSubDir()
    {
        if (self::$subDir !== null) {
            return self::$subDir;
        }
        
        $scriptDir1 = realpath(dirname(dirname(__DIR__)));
        $scriptDir2 = realpath($_SERVER['DOCUMENT_ROOT']);
        $subDir = trim(substr($scriptDir1, strlen($scriptDir2)), "/\\");

        if (!empty($subDir)) {
            $subDir = '/' . $subDir;
        }

        return $subDir;
    }

    public static function setSubDir($subDir)
    {
        self::$subDir = '/' . trim($subDir, '/');
    }
    
    public static function getDomain()
    {
        return !empty(self::$domain) ? self::$domain : rtrim($_SERVER['HTTP_HOST']);;
    }
    
    public static function setDomain($domain)
    {
        self::$domain = $domain;
    }
    
    public static function setProtocol($protocol)
    {
        self::$protocol = $protocol;
    }

    private static function getProtocol()
    {
        
        if (!empty(self::$protocol)) {
            return self::$protocol;
        }
        
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5)) == 'https' ? 'https' : 'http';
        if ($_SERVER["SERVER_PORT"] == 443) {
            $protocol = 'https';
        } elseif (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))){
            $protocol = 'https';
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'){
            $protocol = 'https';
        }
        
        return $protocol;
    }
    
    /**
     * Проверка сессии
     */
    public function checkSession()
    {
        if (!empty($_POST)) {
            if (empty($_POST['session_id']) || $_POST['session_id'] != session_id()) {
                unset($_POST);
                return false;
            }
        }
        return true;
    }
    
    /**
     * Формирование ссылки
     * 
     * @var array $params
     * @return string
     */
    public function url($params = [])
    {
        $query = [];
        $url = @parse_url($_SERVER["REQUEST_URI"]);

        if (isset($params['path'])) {
            $url['path'] = @parse_url($params['path'], PHP_URL_PATH);
            unset($params['path']);
        }

        if (!empty($url['query'])) {
            parse_str($url['query'], $query);
        }
        
        foreach($params as $name=>$value) {
            $query[$name] = $value;
        }
        
        $queryIsEmpty = true;
        foreach ($query as $name=>$value) {
            if ($value!=='' && $value!==null) {
                $queryIsEmpty = false;
            }
        }
        
        if (!$queryIsEmpty) {
            $url['query'] = http_build_query($query);
        } else {
            $url['query'] = null;
        }

        return http_build_url(null, $url);
    }
}
