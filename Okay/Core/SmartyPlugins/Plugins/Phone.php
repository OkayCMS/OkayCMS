<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Phone as PhoneCore;
use Okay\Core\SmartyPlugins\Modifier;

class Phone extends Modifier
{

    /**
     * @var Phone
     */
    private $phone;
    
    public function __construct(PhoneCore $phone)
    {
        $this->phone = $phone;
    }

    public function run($phoneNumber, $numberFormat = null)
    {
        return $this->phone->format($phoneNumber, $numberFormat);
    }
}