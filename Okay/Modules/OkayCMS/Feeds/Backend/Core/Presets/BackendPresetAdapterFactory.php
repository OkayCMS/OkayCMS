<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets;

use Okay\Core\Entity\Entity;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\ServiceLocator;

class BackendPresetAdapterFactory
{
    /** @var array */
    private $presets;

    /** @var array */
    private $presetAdapters = [];

    public function __construct(
        array $presets
    ) {
        $this->presets = $presets;
    }

    public function get(string $presetName): BackendPresetAdapterInterface
    {
        $this->set($presetName);
        $presetAdapter = $this->presetAdapters[$presetName];

        return ExtenderFacade::execute(__METHOD__, $presetAdapter, func_get_args());
    }

    private function set(string $presetName): void
    {
        if (empty($this->presetAdapters[$presetName])) {
            $this->create($presetName);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function create(string $presetName): void
    {
        if (empty($this->presets[$presetName]['backend_adapter'])) {
            throw new \Exception("Preset {$presetName} doesn't have backend adapter.");
        } else if (!class_exists($this->presets[$presetName]['backend_adapter'])) {
            throw new \Exception("The backend adapter for {$presetName} preset is not a class.");
        }

        $arguments = $this->getMethodArguments(new \ReflectionMethod($this->presets[$presetName]['backend_adapter'], '__construct'));

        $reflector = new \ReflectionClass($this->presets[$presetName]['backend_adapter']);
        $this->presetAdapters[$presetName] = $reflector->newInstanceArgs($arguments);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function getMethodArguments(\ReflectionFunctionAbstract $reflectionFunction): array
    {
        $serviceLocator = ServiceLocator::getInstance();

        /** @var EntityFactory $entityFactory */
        $entityFactory = $serviceLocator->getService(EntityFactory::class);

        return array_reduce($reflectionFunction->getParameters(), function($arguments, $parameter) use ($serviceLocator, $entityFactory, $reflectionFunction) {
            /** @var \ReflectionParameter $parameter */
            if (($type = $parameter->getType()) !== null) {
                $typeName = $type->getName();
                if ($serviceLocator->hasService($typeName)) {
                    $arguments[] = $serviceLocator->getService($typeName);
                } elseif (is_subclass_of($typeName, Entity::class)) {
                    $arguments[] = $entityFactory->get($typeName);
                } elseif (class_exists($typeName)) {
                    $arguments[] = new $typeName;
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Missing argument \"\${$parameter->name}\" in function \"{$reflectionFunction->getName()}\".");
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = null;
            }

            return $arguments;
        }, []);
    }

    public function getPresets(): array
    {
        return ExtenderFacade::execute(__METHOD__, $this->presets, func_get_args());
    }
}