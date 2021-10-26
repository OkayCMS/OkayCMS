<?php

namespace Okay\Core\OkayContainer;

use Psr\Container\ContainerInterface as PsrContainerInterface;


interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Retrieve a parameter from the container.
     * @param string $name The parameter name.
     * @return mixed The parameter.
     */
    public function getParameter(string $name);

    /**
     * Check to see if the container has a parameter.
     * @param string $name The parameter name.
     * @return bool True if the container has the parameter, false otherwise.
     */
    public function hasParameter(string $name): bool;
    
    
    public function bindService($name, $service);
    
    public function bindServices(array $services);
}
