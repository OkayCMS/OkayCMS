<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Modifier;

class FirstLetter extends Modifier
{
    protected $tag = 'first_letter';

    public function run($str)
    {
        return function_exists("mb_substr") ? mb_substr($str, 0, 1) : "";
    }
}