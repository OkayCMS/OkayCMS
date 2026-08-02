<?php

namespace Okay\Core\OkayContainer;

use Okay\Core\Entity\Entity;
use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;

trait MethodDI
{
    /**
     * @return list<mixed>
     */
    private function getMethodArguments(\ReflectionFunctionAbstract $reflectionFunction): array
    {
        $serviceLocator = ServiceLocator::getInstance();

        /** @var EntityFactory $entityFactory */
        $entityFactory = $serviceLocator->getService(EntityFactory::class);

        return array_reduce($reflectionFunction->getParameters(), function ($arguments, $parameter) use ($serviceLocator, $entityFactory, $reflectionFunction) {
            /** @var \ReflectionParameter $parameter */
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
                if ($serviceLocator->hasService($typeName)) {
                    $arguments[] = $serviceLocator->getService($typeName);
                } elseif (is_subclass_of($typeName, Entity::class)) {
                    $arguments[] = $entityFactory->get($typeName);
                } elseif (class_exists($typeName)) {
                    $arguments[] = new $typeName();
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Missing argument \"\${$parameter->name}\" in function \"{$reflectionFunction->getName()}\".");
                }
            } elseif ($type !== null) {
                throw new \Exception("Missing argument \"\${$parameter->name}\" in function \"{$reflectionFunction->getName()}\" (union/intersection types are not supported).");
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = null;
            }

            return $arguments;
        }, []);
    }
}
