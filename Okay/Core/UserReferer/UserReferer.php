<?php


namespace Okay\Core\UserReferer;


use Okay\Core\Request;
use Snowplow\RefererParser\Config\JsonConfigReader;
use Snowplow\RefererParser\Parser;
use Snowplow\RefererParser\Referer;

class UserReferer
{
    
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SEARCH = 'search';
    const CHANNEL_SOCIAL = 'social';
    const CHANNEL_REFERRAL = 'referral';
    const CHANNEL_UNKNOWN = 'unknown';
    
    /** @var Parser */
    private $parser;
    
    private static $userReferer;
    
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function parse()
    {
        $userReferer = null;
        /** @var Referer $referer */
        $referer = $this->parser->parse(
            Request::getReferer(),
            Request::getCurrentUrl()
        );

        if ($referer->isKnown()) {
            switch ($referer->getMedium()) {
                case self::CHANNEL_EMAIL :
                    $userReferer = [
                        'medium' => self::CHANNEL_EMAIL,
                        'source' => $referer->getSource(),
                    ];
                    break;
                case self::CHANNEL_SEARCH :
                    $userReferer = [
                        'medium' => self::CHANNEL_SEARCH,
                        'source' => $referer->getSource(),
                    ];
                    break;
                case self::CHANNEL_SOCIAL :
                    $userReferer = [
                        'medium' => self::CHANNEL_SOCIAL,
                        'source' => $referer->getSource(),
                    ];
                    break;
            }
        } elseif (($referer = Request::getReferer()) && !$this->isInternalUrl($referer)) {
            $userReferer = [
                'medium' => self::CHANNEL_REFERRAL,
                'source' => parse_url($referer, PHP_URL_HOST),
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
        setcookie('userReferer', base64_encode(json_encode($referer)), time()+60*60*24*3, '/', '', false, false);
    }
    
    public function isInternalUrl($url)
    {
        return parse_url($url, PHP_URL_HOST) == Request::getDomain();
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
    
    public static function createConfigReader()
    {
        return new JsonConfigReader(__DIR__ . '/data/referers.json');
    }
}
