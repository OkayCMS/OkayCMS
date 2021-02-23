<?php


namespace Okay\Core\Adapters\Response;


class Json extends AbstractResponse
{

    public function getSpecialHeaders()
    {
        return [
            'Content-type: application/json; charset=utf-8',
            'Cache-Control: must-revalidate',
            'Pragma: no-cache',
            'Expires: -1',
        ];
    }
    
    public function send($content)
    {
        // todo добавить json_encode()
        print implode('', $content);
    }
}
