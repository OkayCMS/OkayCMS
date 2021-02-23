<?php


namespace Okay\Core\Adapters\Response;


use Okay\Core\Adapters\AbstractAdapterManager;

class AdapterManager extends AbstractAdapterManager
{
    
    protected function createAdapter($adapterName)
    {
        $adapterClass = __NAMESPACE__.'\\'.$adapterName;
        $this->adapter = new $adapterClass();
    }

}
