<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Modifier;

class Balance extends Modifier
{
    public function run($minutes = 0, $signed = true) {
        $sign = '';
        if ($signed === true) {
            if ($minutes > 0) {
                $sign = '+';
            } elseif ($minutes < 0) {
                $sign = '-';
            }
        }
        
        $minutes = abs($minutes);
        $hours = intval(floor($minutes/60));
        $minutes -= $hours*60;
        return $sign.($hours < 10 ? '0' : '').$hours.':'.($minutes < 10 ? '0' : '').$minutes;
    }
}