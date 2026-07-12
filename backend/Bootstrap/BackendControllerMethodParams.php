<?php

declare(strict_types=1);

namespace Okay\Admin\Bootstrap;

use Okay\Core\Entity\Entity;
use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;

/**
 * Resolves controller method parameters via reflection for the admin bootstrap.
 */
final class BackendControllerMethodParams
{
    /**
     * @param object $controller Controller instance (Reflection resolves declared parameter types)
     *
     * @return list<mixed>
     */
    public static function resolve(
        object $controller,
        string $methodName,
        ServiceLocator $serviceLocator,
        EntityFactory $entityFactory,
    ): array {
        $methodParams = [];

        $reflectionMethod = new \ReflectionMethod($controller, $methodName);
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterType = $parameter->getType();
            if (!$parameterType instanceof \ReflectionNamedType) {
                continue;
            }
            $parameterName = $parameterType->getName();
            if (is_subclass_of($parameterName, Entity::class)) {
                $methodParams[] = $entityFactory->get($parameterName);
            } else {
                $methodParams[] = $serviceLocator->getService($parameterName);
            }
        }

        return $methodParams;
    }
}
