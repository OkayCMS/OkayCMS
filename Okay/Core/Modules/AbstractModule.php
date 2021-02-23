<?php


namespace Okay\Core\Modules;


use Okay\Core\Design;
use Okay\Core\ServiceLocator;

abstract class AbstractModule
{
    /** @var Design */
    protected $design;
    
    public function __construct()
    {
        $SL = ServiceLocator::getInstance();
        $this->design = $SL->getService(Design::class);
    }
    
}