<?php


namespace Okay\Core;


class Translit
{
    private static $translitPairs = [
        // русский
        [
            'from'  => "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я",
            'to'    => "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya"
        ],
        // грузинский
        [
            'from'  => "ა-ბ-გ-დ-ე-ვ-ზ-თ-ი-კ-ლ-მ-ნ-ო-პ-ჟ-რ-ს-ტ-უ-ფ-ქ-ღ-ყ-შ-ჩ-ც-ძ-წ-ჭ-ხ-ჯ-ჰ",
            'to'    => "a-b-g-d-e-v-z-th-i-k-l-m-n-o-p-zh-r-s-t-u-ph-q-gh-qh-sh-ch-ts-dz-ts-tch-kh-j-h"
        ],

    ];

    private static $specPairs = [
        '+'  => 'p',
        '-'  => 'm',
        '—'  => 'ha',
        '–'  => 'hb',
        '−'  => 'hc',
        '‐'  => 'hd',
        '/'  => 'f',
        '°'  => 'deg',
        '±'  => 'pm',
        '_'  => 'u',
        '.'  => 'd',
        ','  => 'c',
        '@'  => 'at',
        '('  => 'lb',
        ')'  => 'rb',
        '{'  => 'lf',
        '}'  => 'rf',
        '['  => 'ls',
        ']'  => 'rs',
        ';'  => 'sem',
        ':'  => 'col',
        '%'  => 'pe',
        '$'  => 'do',
        ' '  => 'sp',
        '?'  => 'w',
        '&'  => 'a',
        '*'  => 's',
        '®'  => 'r',
        '©'  => 'co',
        '\'' => 'ap',
        '"'  => 'qu',
        '`'  => 'bt',
        '<'  => 'le',
        '>'  => 'mo',
        '#'  => 'sh',
        '№'  => 'n',
        '!'  => 'em',
        '~'  => 't',
        '^'  => 'h',
        '='  => 'eq',
        '|'  => 'vs',

    ];

    public static function getTranslitPairs()
    {
        return self::$translitPairs;
    }
    
    public static function translit($text)
    {
        $res = $text;
        foreach (self::getTranslitPairs() as $pair) {
            $from = explode('-', $pair['from']);
            $to = explode('-', $pair['to']);
            $res = str_replace($from, $to, $res);
        }

        $res = preg_replace("/[\s]+/ui", '-', $res);
        $res = preg_replace("/[^a-zA-Z0-9\.\-\_]+/ui", '', $res);
        $res = strtolower($res);
        return $res;
    }

    public static function translitAlpha($text)
    {
        $res = $text;
        foreach (self::getTranslitPairs() as $pair) {
            $pair['from'] = explode('-', $pair['from']);
            $pair['to'] = explode('-', $pair['to']);

            $pair = self::specPairs($pair);

            $res = str_replace($pair['from'], $pair['to'], $res);
        }

        $res = preg_replace("/[\s]+/ui", '', $res);
        $res = preg_replace("/[^a-zA-Z0-9]+/ui", '', $res);
        $res = strtolower($res);
        return $res;
    }

    //Добавляет к массиву пар для транслита, пары для замены спецсимволов на буквенные обозначения
    private static function specPairs($pair) {
        foreach (self::$specPairs as $symbol => $alias) {
            $pair['from'][] = $symbol;
            $pair['to'][]   = $alias;
        }

        return $pair;
    }
    
}