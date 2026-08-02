<?php

namespace Okay\Core\Modules\Extender;

use Okay\Core\DebugBar\DebugBar;
use Okay\Core\ServiceLocator;

class ChainExtender extends AbstractExtender
{
    protected static $triggers = [];

    /**
     * @param list<mixed> $input
     */
    public static function execute($trigger, $output = null, array $input = [])
    {
        if (self::triggerNotUse($trigger)) {
            return $output;
        }

        $extensions = array_values(static::$triggers[$trigger]);

        $serviceLocator = ServiceLocator::getInstance();

        $extendedOutput = null;
        $countExtensions = count($extensions);
        for ($i = 0; $i < $countExtensions; $i++) {
            $currentExtensions = $extensions[$i];

            $classExtender = $currentExtensions->class;
            if ($serviceLocator->hasService($currentExtensions->class)) {
                $classExtender = $serviceLocator->getService($currentExtensions->class);
            } elseif (class_exists($classExtender)) {
                $classExtender = new $classExtender();
            } else {
                throw new \Exception("Class \"{$classExtender}\" not found");
            }

            $callback = [$classExtender, $currentExtensions->method];
            if (!is_callable($callback)) {
                throw new \Exception("Method \"{$currentExtensions->method}\" not found");
            }

            DebugBar::startExtensionExecution($trigger, $currentExtensions);
            if (static::isFirstExtension($i, $extensions)) {
                $extendedOutput = call_user_func_array($callback, array_merge([$output], $input));
            } else {
                $extendedOutput = call_user_func_array($callback, array_merge([$extendedOutput], $input));
            }
            DebugBar::finishExtensionExecution($trigger, $currentExtensions);
        }

        return $extendedOutput;
    }

    private static function triggerNotUse($triggerName)
    {
        return empty(self::$triggers[$triggerName]);
    }

    private static function isFirstExtension($index, $extensions)
    {
        $extensionsIndexes = array_keys($extensions);
        return $index === reset($extensionsIndexes);
    }
}
