<?php


namespace Okay\Core\Adapters\Response;


class ImageWebp extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: image/webp',
        ];
    }
    
    public function send($content)
    {
        print implode('', $content);
    }
}
