<?php


namespace Okay\Core;


use Okay\Core\Adapters\Response\AdapterManager;

class Response
{

    private $content = [];
    private $adapterManager;
    private $headers;
    private $type;
    private $isStream = false;
    private $statusCode = 200;
    
    public function __construct(AdapterManager $adapterManager, string $version)
    {
        $this->adapterManager = $adapterManager;
        $this->type = RESPONSE_HTML;
        $this->addHeader('X-Powered-CMS: OkayCMS ' . $version);
    }

    /**
     * Метод перенаправления на другой ресурс. При помощи функции exit() 
     * прекращает исполнение кода расположенного ниже вызова метода.
     * 
     * @param $resource
     * @param $responseCode
     * 
     * @return void
     * @throws \Exception
     */
    public static function redirectTo($resource, $responseCode = 302)
    {
        $responseCode = (int) $responseCode;

        if (!in_array($responseCode, [301, 302, 307, 308])) {
            throw new \Exception("$responseCode is not valid redirect response code.");
        }

        $headerContent = 'location: '.$resource;
        header($headerContent, false, $responseCode);
        exit();
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function addHeader($headerContent, $replace = true, $responseCode = null)
    {
        $this->headers[] = [$headerContent, $replace, $responseCode];
        return $this;
    }
    
    public function setContent($content, $type = null)
    {
        if ($type !== null) {
            $this->type = trim($type);
        }
        $this->content[] = $content;
        return $this;
    }
    
    public function setContentType($type)
    {
        $this->type = trim($type);
    }
    
    public function getContentType()
    {
        return $this->type;
    }
    
    public function getContent()
    {
        return $this->content;
    }

    /**
     * В отличии от метода sendContent(), этот метод непосредственно сейчас отправляет данные.
     * Нужно обязательно до первого его вызова установить тип данных setContent(), и вызвать sendHeaders()
     * 
     * Пример:
     * $response->setContentType(RESPONSE_XML);
     * $response->sendHeaders();
     * $response->sendStream('<xml>');
     * $response->sendStream('</xml>');
     * 
     * @param string $content
     * @param null $type
     */
    public function sendStream($content, $type = null)
    {
        $this->isStream = true;
        
        if ($type !== null) {
            $this->type = trim($type);
        }
        
        /** @var Adapters\Response\AbstractResponse $adapter */
        $adapter = $this->adapterManager->getAdapter($this->type);

        $adapter->send([$content]);
    }
    
    public function sendContent()
    {
        if ($this->isStream === true) {
            return;
        }
        
        /** @var Adapters\Response\AbstractResponse $adapter */
        $adapter = $this->adapterManager->getAdapter($this->type);

        $this->sendHeaders();
        
        $adapter->send($this->content);
    }

    public function sendHeaders()
    {
        $this->commitStatusCode();

        /** @var Adapters\Response\AbstractResponse $adapter */
        $adapter = $this->adapterManager->getAdapter($this->type);

        // Добавляем специальные заголовки, от драйвера
        foreach ($adapter->getSpecialHeaders() as $header) {
            $this->addHeader($header, true);
        }

        if ($this->headersExists()) {
            foreach ($this->headers as $k => $header) {
                list($headerContent, $replace, $responseCode) = $header;

                if (is_null($responseCode)) {
                    header($headerContent, $replace);
                    continue;
                }

                header($headerContent, $replace, $responseCode);
                unset($this->headers[$k]);
            }
        }
    }

    private function headersExists()
    {
        if (!empty($this->headers)) {
            return true;
        }

        return false;
    }

    public function setHeaderLastModify($lastModify)
    {
        $lastModify = empty($lastModify) ? date("Y-m-d H:i:s") : $lastModify;
        $tmpDate = date_parse($lastModify);
        @$lastModifiedUnix = mktime( $tmpDate['hour'], $tmpDate['minute'], $tmpDate['second '], $tmpDate['month'],$tmpDate['day'],$tmpDate['year'] );

        //Проверка модификации страницы
        $lastModified = gmdate("D, d M Y H:i:s \G\M\T", $lastModifiedUnix);
        $ifModifiedSince = false;

        if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
        }

        if ($ifModifiedSince && $ifModifiedSince >= $lastModifiedUnix) {
            $this->setStatusCode(304)->sendHeaders();
            exit;
        }

        $this->addHeader('Last-Modified: '. $lastModified);
    }

    private function commitStatusCode()
    {
        if (empty($this->statusCode)) {
            throw new \Exception('Response status code cannot be empty');
        }

        switch ($this->statusCode) {
            // 5XX
            case 521:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 521 Web Server Is Down');
                break;
            case 520:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 520 Unknown Error');
                break;
            case 511:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 511 Network Authentication Required');
                break;
            case 510:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 510 Not Extended');
                break;
            case 509:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 509 Bandwidth Limit Exceeded');
                break;
            case 508:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 508 Loop Detected');
                break;
            case 507:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 507 Insufficient Storage');
                break;
            case 506:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 506 Variant Also Negotiates');
                break;
            case 505:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 505 HTTP Version Not Supported');
                break;
            case 504:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 504 Gateway Timeout');
                break;
            case 503:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 503 Service Unavailable');
                break;
            case 502:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 502 Bad Gateway');
                break;
            case 501:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 501 Not Implemented');
                break;
            case 500:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error');
                break;

            // 4XX
            case 423:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 423 Locked');
                break;
            case 417:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 417 Expectation Failed');
                break;
            case 410:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 410 Gone');
                break;
            case 405:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 405 Method Not Allowed');
                break;
            case 404:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                break;
            case 401:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized');
                break;
            case 400:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
                break;

            // 3XX
            case 308:
                throw new \Exception('Please use Okay\Core\Response::redirectTo method for 308 redirect');
                break;
            case 307:
                throw new \Exception('Please use Okay\Core\Response::redirectTo method for 307 redirect');
                break;
            case 304:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
                break;
            case 302:
                throw new \Exception('Please use Okay\Core\Response::redirectTo method for 302 redirect');
                break;
            case 301:
                throw new \Exception('Please use Okay\Core\Response::redirectTo method for 301 redirect');
                break;

            // 2XX
            case 206:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 206 Partial Content');
                break;
            case 205:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 205 Reset Content');
                break;
            case 200:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 200 OK');
                break;

            // 1XX
            case 102:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 100 Continue');
                break;
            case 101:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 101 Switching Protocols');
                break;
            case 100:
                $this->addHeader($_SERVER['SERVER_PROTOCOL'].' 102 Processing');
                break;

            default:
                throw new \Exception('Response status code "'.$this->statusCode.'" incorrect or don`t supported');
        }
    }
}
