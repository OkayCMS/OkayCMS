<?php


namespace Okay\Core\Adapters\Response;


class GptStream extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: text/event-stream;',
            'Cache-Control: no-cache',
//            'Expires: -1',
        ];
    }
    
    public function send($content)
    {
        echo implode('', $content) . "\n\n";
//        ob_flush();
        flush();
    }
}
