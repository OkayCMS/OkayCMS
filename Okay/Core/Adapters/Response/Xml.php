<?php


namespace Okay\Core\Adapters\Response;


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
        print implode('', $content);
    }
}
