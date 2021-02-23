<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Money;
use Okay\Core\SmartyPlugins\Modifier;

class Convert extends Modifier
{

    /**
     * @var Money
     */
    private $money;
    
    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    public function run($price, $currency_id = null, $format = true, $revers = false)
    {
        return $this->money->convert($price, $currency_id, $format, $revers);
    }
}