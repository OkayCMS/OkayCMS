<?php


namespace Okay\Core\Adapters\Response;


class ImageGif extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: image/gif',
        ];
    }
    
    public function send($content)
    {
        print implode('', $content);
    }
}
