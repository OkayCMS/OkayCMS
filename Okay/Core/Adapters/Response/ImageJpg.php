<?php


namespace Okay\Core\Adapters\Response;


class ImageJpg extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: image/jpeg',
        ];
    }
    
    public function send($content)
    {
        print implode('', $content);
    }
}
