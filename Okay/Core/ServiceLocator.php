<?php


namespace Okay\Core;


use Okay\Core\OkayContainer\OkayContainer;

class ServiceLocator
{
    /**
     * @var OkayContainer
     */
    private $DI;
    
    private static $instance;
    private static $isSingleton = false;
    
    public function __construct()
    {
        $this->DI = include 'Okay/Core/config/container.php';
        if (self::$isSingleton === false) {
            trigger_error('Creating a ServiceLocator class through a "new" is Deprecated. Please use ServiceLocator::getInstance(); for use as singleton.', E_USER_DEPRECATED);
        }
        self::$isSingleton = false;
    }

    private function __clone() {}

    public static function getInstance()
    {
        self::$isSingleton = true;
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * @param $service
     * @return object
     */
    public function getService($service)
    {
        return $this->DI->get($service);
    }
    
    public function hasService($service)
    {
        return $this->DI->has($service);
    }
}
