<?php


namespace Okay\Core\Adapters\Response;


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
        print implode('', $content);
    }
}
