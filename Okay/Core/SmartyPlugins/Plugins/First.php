<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Modifier;

class First extends Modifier
{
    public function run($params = [])
    {
        if(!is_array($params)) {
            return false;
        }

        return reset($params);
    }
}