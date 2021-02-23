<?php


namespace Okay\Core\Modules\Extender;


abstract class AbstractExtender
{
    protected static $triggers = [];

    public function newExtension($classExpandable, $methodExpandable, $classExtender, $methodExtender)
    {
        $trigger = self::compileTrigger($classExpandable, $methodExpandable);
        
        if (!method_exists($classExpandable, $methodExpandable)) {
            throw new \Exception("Expandable \"{$classExpandable}::{$methodExpandable}()\" is not a method");
        }
        
        if (! is_callable([$classExtender, $methodExtender])) {
            throw new \Exception("Class {$classExtender}::{$methodExtender} is not callable");
        }

        if (! is_subclass_of($classExtender, ExtensionInterface::class)) {
            throw new \Exception("Class {$classExtender}::class must implements " . ExtensionInterface::class . " interface");
        }

        $extension = new \stdClass();
        $extension->class  = $classExtender;
        $extension->method = $methodExtender;

        static::$triggers[$trigger][] = $extension;
    }

    protected static function compileTrigger($className, $methodName)
    {
        return $className."::".$methodName;
    }

    public static function extensionLog($trigger)
    {
        if (is_array($trigger)) {
            list($className, $methodName) = $trigger;
            $trigger = self::compileTrigger($className, $methodName);
        }

        if (isset(static::$triggers[$trigger])) {
            return static::$triggers[$trigger];
        }

        return [];
    }

    public static function execute($trigger, $output = null, array $input = []) {}
}