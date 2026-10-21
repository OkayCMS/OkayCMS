<?php


namespace Okay\Core\UserReferer;


use Okay\Core\Request;
use Okay\Core\Response;

class UserReferer
{
    
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SEARCH = 'search';
    const CHANNEL_SOCIAL = 'social';
    const CHANNEL_REFERRAL = 'referral';
    const CHANNEL_UNKNOWN = 'unknown';
    
    /** @var RefererParser */
    private $parser;
    
    private static $userReferer;
    
    public function __construct(RefererParser $parser)
    {
        $this->parser = $parser;
    }

    public function parse()
    {
        $userReferer = null;
        $referer = $this->parser->parse(
            Request::getReferer(),
            Request::getCurrentUrl()
        );

        if ($referer->isKnown()) {
            $userReferer = [
                'medium' => $referer->getMedium(),
                'source' => $referer->getSource(),
            ];
        } elseif (($referer = Request::getReferer()) && !$this->isInternalUrl($referer)) {
            $userReferer = [
                'medium' => self::CHANNEL_REFERRAL,
                'source' => parse_url((string) $referer, PHP_URL_HOST),
            ];
        } else {
            $userReferer = [
                'medium' => self::CHANNEL_UNKNOWN,
                'source' => '',
            ];
        }
        
        $this->saveUserReferer($userReferer);
    }
    
    private function saveUserReferer(array $referer)
    {
        self::$userReferer = $referer;
        Response::setCookie('userReferer', base64_encode(json_encode($referer)), ['ttl' => 60*60*24*3, 'secure' => false, 'httponly' => false]);
    }
    
    public function isInternalUrl($url)
    {
        return parse_url((string) $url, PHP_URL_HOST) == Request::getDomain();
    }
    
    public static function getUserReferer()
    {
        if (!empty(self::$userReferer)) {
            return self::$userReferer;
        } elseif (!empty($_COOKIE['userReferer'])) {
            return json_decode(base64_decode($_COOKIE['userReferer']), true);
        }
        
        return null;
    }
}
