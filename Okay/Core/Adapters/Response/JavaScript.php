<?php

namespace Okay\Core\Adapters\Response;

use Okay\Core\DebugBar\DebugBar;

class JavaScript extends AbstractResponse
{
    public function getSpecialHeaders()
    {
        return [
            'Content-type: text/javascript; charset=utf-8',
        ];
    }

    public function send($content)
    {
        self::safeStackData();
        print implode('', $content);
    }
}
