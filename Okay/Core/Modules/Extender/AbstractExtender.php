<?php


namespace Okay\Core\Modules\Extender;


abstract class AbstractExtender
{
    protected static $triggers = [];

    protected $deprecatedMethods = [];

    public function setDeprecated($config)
    {
        $this->deprecatedMethods = [];
        foreach ($config as $association) {
            $this->deprecatedMethods["{$association[0][0]}::{$association[0][1]}"] = $association;
        }
    }

    public function newExtension($classExpandable, $methodExpandable, $classExtender, $methodExtender)
    {
        $trigger = self::compileTrigger($classExpandable, $methodExpandable);

        if ($newMethod = $this->checkAndCorrectDeprecatedMethod($trigger)) {
            list($classExpandable, $methodExpandable) = $newMethod;
            $trigger = self::compileTrigger($classExpandable, $methodExpandable);
        }

        $this->validateExtension($classExpandable, $methodExpandable, $classExtender, $methodExtender);

        $extension = new \stdClass();
        $extension->class  = $classExtender;
        $extension->method = $methodExtender;

        static::$triggers[$trigger][] = $extension;
    }

    protected static function compileTrigger($className, $methodName)
    {
        return $className."::".$methodName;
    }

    protected function checkAndCorrectDeprecatedMethod($trigger)
    {
        $result = false;

        if (isset($this->deprecatedMethods[$trigger])) {
            $association = $this->deprecatedMethods[$trigger];
            $result = $association[1];
            if ($association[1] === false) {
                trigger_error("Method {$association[0][0]}::{$association[0][1]} will be deprecated in the future. Please don't extend it.",
                    E_USER_WARNING);
            } else {
                trigger_error("Method {$association[0][0]}::{$association[0][1]} is deprecated. Please use {$association[0][0]}::{$association[0][1]}.",
                    E_USER_DEPRECATED);
            }
        }

        return $result;
    }

    protected function validateExtension($classExpandable, $methodExpandable, $classExtender, $methodExtender)
    {
        if (!method_exists($classExpandable, $methodExpandable)) {
            throw new \Exception("Expandable \"{$classExpandable}::{$methodExpandable}()\" is not a method");
        }

        if (!is_callable([$classExtender, $methodExtender], true)) {
            throw new \Exception("Method {$classExtender}::{$methodExtender} is not callable");
        }

        if (!is_subclass_of($classExtender, ExtensionInterface::class)) {
            throw new \Exception("Class {$classExtender}::class must implements " . ExtensionInterface::class . " interface");
        }
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