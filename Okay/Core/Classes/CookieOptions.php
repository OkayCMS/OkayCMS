<?php


namespace Okay\Core\Classes;


use Okay\Core\Request;

class CookieOptions
{
    public const DAY     = ['ttl' => 24 * 3600, 'path' => '/'];
    public const WEEK    = ['ttl' => 7 * 24 * 3600, 'path' => '/'];
    public const MONTH   = ['ttl' => 30 * 24 * 3600, 'path' => '/'];
    public const YEAR    = ['ttl' => 365 * 24 * 3600, 'path' => '/'];
    public const SESSION = ['path' => '/'];
    public const DELETE  = ['ttl' => -3600, 'path' => '/'];

    public static function builder(): CookieOptionsBuilder
    {
        return new CookieOptionsBuilder();
    }

    /**
     * @param array|callable $options DAY/WEEK/MONTH/YEAR/SESSION/DELETE, a raw options array, or callable(array $defaults): array
     */
    public static function resolve($options, $value): array
    {
        if (is_callable($options)) {
            $options = $options(self::SESSION);
        }

        return ['value' => (string) $value] + $options + [
            'path'     => '/',
            'domain'   => '',
            'secure'   => Request::isSecure(),
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }
}
