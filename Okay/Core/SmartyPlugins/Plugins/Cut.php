<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Modifier;

class Cut extends Modifier
{    
    public function run($array, $num=1)
    {
        if($num>=0) {
            return array_slice($array, $num, count($array)-$num, true);
        }

        return array_slice($array, 0, count($array)+$num, true);
    }
}