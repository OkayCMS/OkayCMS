<?php


namespace Okay\Core\Modules\Extender;


use Okay\Core\ServiceLocator;

class QueueExtender extends AbstractExtender
{
    protected static $triggers = [];

    public static function execute($trigger, $output = null, array $input = [])
    {
        if (! static::isValidTrigger($trigger)) {
            return;
        }

        $serviceLocator = ServiceLocator::getInstance();
        foreach(static::$triggers[$trigger] as $extension) {

            $classExtender = $extension->class;
            if ($serviceLocator->hasService($extension->class)) {
                $classExtender = $serviceLocator->getService($extension->class);
            } elseif (class_exists($classExtender)) {
                $classExtender = new $classExtender();
            } else {
                throw new \Exception("Class \"{$classExtender}\" not found");
            }
            call_user_func_array([$classExtender, $extension->method], array_merge([$output], $input));
        }
    }

    private static function isValidTrigger($name)
    {
        return isset(static::$triggers[$name]) && is_array(static::$triggers[$name]);
    }
}