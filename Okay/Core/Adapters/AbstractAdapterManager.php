<?php


namespace Okay\Core\Adapters;


abstract class AbstractAdapterManager
{
    protected $defaultAdapterName;
    protected $adapter;

    public function __construct($defaultAdapterName)
    {
        $this->defaultAdapterName = $defaultAdapterName;
    }

    public function getAdapter($adapterName = null)
    {
        $this->setAdapter($adapterName);

        return $this->adapter;
    }

    protected function setAdapter($adapterName = null) // todo type hint
    {
        if (null === $adapterName) {
            $adapterName = $this->defaultAdapterName;
        }

        if ($this->adapter instanceof $adapterName) {
            return;
        }

        // todo исключение, если нет адаптера
        $this->createAdapter($adapterName);
    }
    
    protected function createAdapter($adapterName)
    {
        $reflector = new \ReflectionClass(static::class);
        $adapterClass = $reflector->getNamespaceName().'\\'.$adapterName;
        $this->adapter = new $adapterClass();
    }
    
}