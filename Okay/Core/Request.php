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
        self::$subDir = $subDir;
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

/*Обьявление функции построения ссылки*/
if (!function_exists('http_build_url')) {
    define('HTTP_URL_REPLACE', 1);                // Replace every part of the first URL when there's one of the second URL
    define('HTTP_URL_JOIN_PATH', 2);            // Join relative paths
    define('HTTP_URL_JOIN_QUERY', 4);            // Join query strings
    define('HTTP_URL_STRIP_USER', 8);            // Strip any user authentication information
    define('HTTP_URL_STRIP_PASS', 16);            // Strip any password authentication information
    define('HTTP_URL_STRIP_AUTH', 32);            // Strip any authentication information
    define('HTTP_URL_STRIP_PORT', 64);            // Strip explicit port numbers
    define('HTTP_URL_STRIP_PATH', 128);            // Strip complete path
    define('HTTP_URL_STRIP_QUERY', 256);        // Strip query string
    define('HTTP_URL_STRIP_FRAGMENT', 512);        // Strip any fragments (#identifier)
    define('HTTP_URL_STRIP_ALL', 1024);            // Strip anything but scheme and host
    
    // Build an URL
    // The parts of the second URL will be merged into the first according to the flags argument.
    //
    // @param    mixed            (Part(s) of) an URL in form of a string or associative array like parse_url() returns
    // @param    mixed            Same as the first argument
    // @param    int                A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
    // @param    array            If set, it will be filled with the parts of the composed url like parse_url() would return
    function http_build_url($url, $parts=[], $flags=HTTP_URL_REPLACE, &$new_url=false) {
        $keys = array('user','pass','port','path','query','fragment');
        
        // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
        if ($flags & HTTP_URL_STRIP_ALL) {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
            $flags |= HTTP_URL_STRIP_PORT;
            $flags |= HTTP_URL_STRIP_PATH;
            $flags |= HTTP_URL_STRIP_QUERY;
            $flags |= HTTP_URL_STRIP_FRAGMENT;
        }
        // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
        else if ($flags & HTTP_URL_STRIP_AUTH) {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
        }
        
        // Parse the original URL
        $parse_url = parse_url($url);
        
        // Scheme and Host are always replaced
        if (isset($parts['scheme'])) {
            $parse_url['scheme'] = $parts['scheme'];
        }
        if (isset($parts['host'])) {
            $parse_url['host'] = $parts['host'];
        }
        
        // (If applicable) Replace the original URL with it's new parts
        if ($flags & HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $parse_url[$key] = $parts[$key];
                }
            }
        } else {
            // Join the original URL path with the new path
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
                if (isset($parse_url['path'])) {
                    $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
                } else {
                    $parse_url['path'] = $parts['path'];
                }
            }
            
            // Join the original query string with the new query string
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
                if (isset($parse_url['query'])) {
                    $parse_url['query'] .= '&' . $parts['query'];
                } else {
                    $parse_url['query'] = $parts['query'];
                }
            }
        }
        
        // Strips all the applicable sections of the URL
        // Note: Scheme and Host are never stripped
        foreach ($keys as $key) {
            if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key))) {
                unset($parse_url[$key]);
            }
        }
        
        $new_url = $parse_url;
        
        return
             ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
        ;
    }
}

if(!function_exists('http_build_query')) {
    function http_build_query($data,$prefix=null,$sep='',$key='') {
        $ret = [];
        foreach((array)$data as $k => $v) {
            $k    = urlencode($k);
            if(is_int($k) && $prefix != null) {
                $k    = $prefix.$k;
            };
            if(!empty($key)) {
                $k    = $key."[".$k."]";
            };
            
            if(is_array($v) || is_object($v)) {
                array_push($ret,http_build_query($v,"",$sep,$k));
            } else {
                array_push($ret,$k."=".urlencode($v));
            };
        };
        
        if(empty($sep)) {
            $sep = ini_get("arg_separator.output");
        };
        
        return    implode($sep, $ret);
    };
};
