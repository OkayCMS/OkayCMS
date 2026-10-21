<?php


namespace Okay\Helpers\Import;


class Csv
{
     /**
     * Enclosure and escaping defaults used by PHP.
     * Starting with PHP 8.4, these must be passed explicitly; the default $escape behavior will change.
     */

    public const ENCLOSURE = '"';
    public const ESCAPE    = '\\';

    /**
     * @param resource $stream
     * @param string $delimiter
     * @param int|null $length
     * @return array|false
     */
    public static function read($stream, $delimiter = ',', $length = null)
    {
        return fgetcsv($stream, (int)$length, $delimiter, self::ENCLOSURE, self::ESCAPE);
    }

    /**
     * @param resource $stream
     * @param array $fields
     * @param string $delimiter
     * @return int|false
     */
    public static function write($stream, array $fields, $delimiter = ',')
    {
        return fputcsv($stream, $fields, $delimiter, self::ENCLOSURE, self::ESCAPE);
    }
}
