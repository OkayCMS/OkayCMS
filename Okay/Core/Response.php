<?php


namespace Okay\Core;


use Okay\Core\Adapters\Response\AdapterManager;
use Okay\Core\Modules\LicenseModulesTemplates;

class Response
{
    
    private $content = [];
    private $adapterManager;
    private $headers;
    private $type;
    private $isStream = false;
    private $statusCode = 200;

    private const STATUS_CODES_MESSAGES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',

        304 => 'Not Modified',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Content',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        520 => 'Unknown Error',
        521 => 'Web Server Is Down',
    ];
    
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
     * @param string $resource
     * @param int $responseCode
     *
     * @return void
     * @throws \Exception
     */
    public static function redirectTo(string $resource, int $responseCode = 302): void
    {
        if (!in_array($responseCode, [301, 302, 303, 307, 308])) {
            throw new \Exception("$responseCode is not a valid redirect response code.");
        }
        
        $headerContent = 'Location: ' . $resource;
        header($headerContent, false, $responseCode);
        exit;
    }
    
    public function setStatusCode($statusCode): self
    {
        if (empty($statusCode)) {
            throw new \Exception('Response status code cannot be empty');
        } elseif (in_array($statusCode, [301, 302, 303, 307, 308])) {
            throw new \Exception("Please use Okay\\Core\\Response::redirectTo method for $statusCode redirect");
        } elseif (empty(self::STATUS_CODES_MESSAGES[$statusCode])) {
            throw new \Exception("Response status code $statusCode is invalid or not supported");
        }
        $this->statusCode = $statusCode;
        return $this;
    }
    
    public function addHeader($headerContent, $replace = true, $responseCode = null): self
    {
        $this->headers[] = [$headerContent, $replace, $responseCode];
        return $this;
    }
    
    public function setContent($content, $type = null): self
    {
        if ($type !== null) {
            $this->type = trim($type);
        }
        $this->content[] = $content;
        return $this;
    }
    
    public function setContentType(string $type): self
    {
        $this->type = trim($type);
        return $this;
    }
    
    public function getContentType(): string
    {
        return $this->type;
    }
    
    public function getContent(): array
    {
        return $this->content;
    }
    
    /**
     * В отличие от метода sendContent(), этот метод непосредственно сейчас отправляет данные.
     * Нужно обязательно до первого его вызова установить тип данных setContent(), и вызвать sendHeaders()
     *
     * Пример:
     * $response->setContentType(RESPONSE_XML);
     * $response->sendHeaders();
     * $response->sendStream('<xml>');
     * $response->sendStream('</xml>');
     *
     * @param string $content
     * @param string|null $type
     */
    public function sendStream(string $content, string $type = null): void
    {
        $this->isStream = true;
        
        if ($type !== null) {
            $this->type = trim($type);
        }
        
        /** @var Adapters\Response\AbstractResponse $adapter */
        $adapter = $this->adapterManager->getAdapter($this->type);
        
        $adapter->send([$content]);
    }
    
    public function sendContent(): self
    {
        if ($this->isStream === true) {
            return $this;
        }
        
        /** @var Adapters\Response\AbstractResponse $adapter */
        $adapter = $this->adapterManager->getAdapter($this->type);
        
        $this->sendHeaders();
        
        $adapter->send($this->content);

        return $this;
    }
    
    public function sendHeaders(): self
    {
//        return $this;
        $this->commitStatusCode();
        
        /** @var Adapters\Response\AbstractResponse $adapter */
        $adapter = $this->adapterManager->getAdapter($this->type);
        
        // Добавляем специальные заголовки, от драйвера
        foreach ($adapter->getSpecialHeaders() as $header) {
            $this->addHeader($header);
        }
        
        if (!empty($this->headers)) {
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
        return $this;
    }
    
    public function setHeaderLastModify(string $lastModify): self
    {
        $lastModifiedUnix = $lastModify ? strtotime($lastModify) : time();
        
        // Проверка модификации страницы
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
        
        $this->addHeader('Last-Modified: ' . $lastModified);
        
        return $this;
    }
    
    private function commitStatusCode(): void
    {
        $this->addHeader(sprintf('%s %d %s',
            $_SERVER['SERVER_PROTOCOL'],
            $this->statusCode,
            self::STATUS_CODES_MESSAGES[$this->statusCode]
        ));
    }
}
