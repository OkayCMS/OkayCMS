<?php


namespace Okay\Core\Adapters\Response;


class ImagePng extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: image/png',
        ];
    }
    
    public function send($content)
    {
        print implode('', $content);
    }
}
