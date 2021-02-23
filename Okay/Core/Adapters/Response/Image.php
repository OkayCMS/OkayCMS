<?php


namespace Okay\Core\Adapters\Response;


class Image extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: image',
        ];
    }
    
    public function send($content)
    {
        print implode('', $content);
    }
}
