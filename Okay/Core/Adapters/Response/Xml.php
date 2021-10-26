<?php


namespace Okay\Core\Adapters\Response;


use Okay\Core\DebugBar\DebugBar;

class Xml extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: text/xml; charset=UTF-8',
        ];
    }
    
    public function send($content)
    {
        DebugBar::stackData();
        print implode('', $content);
    }
}
