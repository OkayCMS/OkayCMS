<?php


namespace Okay\Core;


use Okay\Core\Entity\Entity;
use Psr\Log\LoggerInterface;

class EntityFactory
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    private static $objects = [];
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $class
     * @return object
     * @throws \Exception
     */
    public function get($class)
    {
        if (!class_exists($class)) {
            throw new \Exception("Class \"{$class}\" is not exists");
        }

        if (!is_subclass_of($class, Entity::class)) {
            throw new \Exception("Class \"{$class}\" must be subclass of \"Okay\Core\Entity\Entity\" class");
        }

        if (empty(self::$objects[$class])) {
            self::$objects[$class] = $this->create($class);
        }

        return self::$objects[$class];
    }

    private function create($class)
    {   
        if ($this->hasEntity($class)) {
            return new $class();
        }

        throw new \Exception("Entity '{$class}' not exists");
    }
    
    private function hasEntity($class) {
        if (!class_exists($class)) {
            $this->logger->critical("Entity '{$class}' not exists");
            return false;
        }
        
        return true;
    }
    
}
