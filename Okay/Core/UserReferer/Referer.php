<?php


namespace Okay\Core\UserReferer;


class Referer
{
    const MEDIUM_UNKNOWN  = 'unknown';
    const MEDIUM_INTERNAL = 'internal';
    const MEDIUM_INVALID  = 'invalid';

    /** @var string */
    protected $medium;

    /** @var string|null */
    protected $source;

    /** @var string|null */
    protected $searchTerm;

    protected function __construct()
    {
    }

    public static function createKnown($medium, $source, $searchTerm = null)
    {
        $referer = new self();
        $referer->medium = $medium;
        $referer->source = $source;
        $referer->searchTerm = $searchTerm;

        return $referer;
    }

    public static function createUnknown()
    {
        $referer = new self();
        $referer->medium = self::MEDIUM_UNKNOWN;

        return $referer;
    }

    public static function createInternal()
    {
        $referer = new self();
        $referer->medium = self::MEDIUM_INTERNAL;

        return $referer;
    }

    public static function createInvalid()
    {
        $referer = new self();
        $referer->medium = self::MEDIUM_INVALID;

        return $referer;
    }

    /** @return bool */
    public function isValid()
    {
        return $this->medium !== self::MEDIUM_INVALID;
    }

    /** @return bool */
    public function isKnown()
    {
        return !in_array(
            $this->medium,
            [self::MEDIUM_UNKNOWN, self::MEDIUM_INTERNAL, self::MEDIUM_INVALID],
            true
        );
    }

    /** @return string */
    public function getMedium()
    {
        return $this->medium;
    }

    /** @return string|null */
    public function getSource()
    {
        return $this->source;
    }

    /** @return string|null */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }
}
