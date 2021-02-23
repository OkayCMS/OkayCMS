<?php


namespace Okay\Core\Adapters\Response;


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
        print implode('', $content);
    }
}
