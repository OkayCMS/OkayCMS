<?php


namespace Okay\Core\Modules;


use Okay\Core\QueryFactory\Select;

abstract class AbstractModuleEntityFilter
{
    /** @var Select */
    protected $select;
    
    final public function setSelect(Select $select)
    {
        $this->select = $select;
    }
    
}