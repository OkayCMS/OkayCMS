<?php


namespace Okay\Core\Modules\Extender;


class ExtenderFacade
{
    private $queueExtender;
    private $chainExtender;

    public function __construct(QueueExtender $queueExtender, ChainExtender $chainExtender)
    {
        $this->queueExtender = $queueExtender;
        $this->chainExtender = $chainExtender;
    }

    public static function execute($trigger, $output = null, array $input = [])
    {
        if (is_array($trigger)) {
            $trigger = self::stringifyTrigger($trigger);
        }

        $extendedResult = ChainExtender::execute($trigger, $output, $input);
        QueueExtender::execute($trigger, $extendedResult, $input);
        return $extendedResult;
    }

    public function newChainExtension($expandable, $extension)
    {
        list($extendableClass, $extendableMethod) = $this->matchExtensionBindings($expandable);
        list($extensionClass,  $extensionMethod)  = $this->matchExtensionBindings($extension);
        $this->chainExtender->newExtension($extendableClass, $extendableMethod, $extensionClass, $extensionMethod);
    }

    public function newQueueExtension($expandable, $extension)
    {
        list($extendableClass, $extendableMethod) = $this->matchExtensionBindings($expandable);
        list($extensionClass,  $extensionMethod)  = $this->matchExtensionBindings($extension);
        $this->queueExtender->newExtension($extendableClass, $extendableMethod, $extensionClass, $extensionMethod);
    }

    public static function queueExtLog($trigger)
    {
        return QueueExtender::extensionLog($trigger);
    }

    public static  function chainExtLog($trigger)
    {
        return ChainExtender::extensionLog($trigger);
    }

    private static function stringifyTrigger($trigger)
    {
        list($className, $methodName) = $trigger;
        return $className.'::'.$methodName;
    }

    private function matchExtensionBindings($bindings)
    {
        if (isset($bindings['class']) && isset($bindings['method'])) {
            return [$bindings['class'], $bindings['method']];
        }

        return $bindings;
    }
}