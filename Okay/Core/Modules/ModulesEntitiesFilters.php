<?php


namespace Okay\Core\Modules;


use \Exception;

class ModulesEntitiesFilters
{
    private $modulesFilters = [];

    public function __construct() {}
    
    public function registerFilter($entityClassName, $filterName, $filterClassName, $filterMethod)
    {

        if (!is_subclass_of($filterClassName, AbstractModuleEntityFilter::class)) {
            throw new \Exception("Class \"$filterClassName\" must be subclass of " . AbstractModuleEntityFilter::class);
        }
        
        if (!is_callable([$filterClassName, $filterMethod])) {
            throw new \Exception("Method \"$filterMethod->$filterMethod()\" must be callable");
        }
        
        if (!class_exists($entityClassName)) {
            throw new Exception("\"$entityClassName\" is not valid Entity class name");
        }
        
        return $this->modulesFilters[$entityClassName][$filterName] = [$filterClassName, $filterMethod];
    }
    
    public function hasFilter($entityClassName, $filterName)
    {
        return !empty($this->modulesFilters[$entityClassName][$filterName]);
    }

    /**
     * возвращает имя класса, в котором описан метод фильтра
     * @param $entityClassName
     * @param $filterName
     * @return mixed
     */
    public function getFilterClassName($entityClassName, $filterName)
    {
        return $this->modulesFilters[$entityClassName][$filterName][0];
    }

    /**
     * возвращает имя метода, в котором описан фильтр
     * @param $entityClassName
     * @param $filterName
     * @return mixed
     */
    public function getFilterMethod($entityClassName, $filterName)
    {
        return $this->modulesFilters[$entityClassName][$filterName][1];
    }
}