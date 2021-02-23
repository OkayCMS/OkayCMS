<?php


namespace Okay\Core\Adapters\Response;


abstract class AbstractResponse
{
    abstract public function send($content);
    
    abstract public function getSpecialHeaders();
}
