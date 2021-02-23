<?php


namespace Okay\Core\Adapters\Response;


class ImageSvg extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: image/svg+xml',
        ];
    }
    
    public function send($content)
    {
        print implode('', $content);
    }
}
