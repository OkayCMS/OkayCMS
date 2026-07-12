<?php

namespace Okay\Core\Adapters\Response;

use Okay\Core\DebugBar\DebugBar;

class Text extends AbstractResponse
{
    public function getSpecialHeaders()
    {
        return [
            'Content-type: text/plain; charset=utf-8',
        ];
    }

    public function send($content)
    {
        self::safeStackData();
        print implode('', $content);
    }
}
