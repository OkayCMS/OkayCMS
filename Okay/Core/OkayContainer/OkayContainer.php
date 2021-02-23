<?php

namespace Okay\Core\OkayContainer;


use Okay\Core\OkayContainer\Exception\ContainerException;
use Okay\Core\OkayContainer\Exception\ParameterNotFoundException;
use Okay\Core\OkayContainer\Exception\ServiceNotFoundException;
use Okay\Core\OkayContainer\Reference\ParameterReference;
use Okay\Core\OkayContainer\Reference\ServiceReference;
use Okay\Core\Config;
use Okay\Core\Settings;

/**
 * A very simple dependency injection container.
 */
class OkayContainer implements ContainerInterface
{
    /**
     * @var array
     */
    private $services;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array
     */
    private $serviceStore;

    public static function getInstance($services = [], $parameters = [])
    {
        
        if (empty(self::$instance)) {
            self::$instance = new self($services, $parameters);
        }

        return self::$instance;
    }
    
    private function __construct(array $services = [], array $parameters = [])
    {
        $this->services     = $services;
        $this->parameters   = $parameters;
        $this->serviceStore = [];
    }
    private function __clone(){}

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        
        if (!$this->has($name)) {
            throw new ServiceNotFoundException('Service not found: '.$name);
        }

        // If we haven't created it, create it and save to store
        if (!isset($this->serviceStore[$name])) {
            $this->serviceStore[$name] = $this->createService($name);
            if (isset($this->services[$name]['calls'])) {
                $this->initializeService($this->serviceStore[$name], $name, $this->services[$name]['calls']);
            }
        }

        // Return service from store
        return $this->serviceStore[$name];
    }

    public function bindService($name, $service)
    {
        $this->services[$name] = $service;
    }
    
    
    public function bindServices(array $services)
    {
        $this->services = array_merge($this->services, $services);
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParameter($name)
    {
        $tokens  = explode('.', $name);
        $context = $this->parameters;

        while (null !== ($token = array_shift($tokens))) {
            if (!isset($context[$token])) {
                throw new ParameterNotFoundException('Parameter not found: ' . $name);
            }

            $context = $context[$token];
        }

        return $this->configParameters($context);
    }

    /**
     * {@inheritDoc}
     */
    public function hasParameter($name)
    {
        try {
            $this->getParameter($name);
        } catch (ParameterNotFoundException $exception) {
            return false;
        }

        return true;
    }
    
    /**
     * Attempt to create a service.
     * @param string $name The service name.
     * @return mixed The created service.
     * @throws ContainerException On failure.
     * @throws \ReflectionException
     */
    private function createService($name)
    {
        $entry = &$this->services[$name];

        if (!is_array($entry) || !isset($entry['class'])) {
            throw new ContainerException($name.' service entry must be an array containing a \'class\' key');
        } elseif (!class_exists($entry['class'])) {
            throw new ContainerException($name.' service class does not exist: '.$entry['class']);
        } elseif (isset($entry['lock'])) {
            throw new ContainerException($name.' contains circular reference');
        }

        $entry['lock'] = true;

        $arguments = isset($entry['arguments']) ? $this->resolveArguments($entry['arguments']) : [];

        $reflector = new \ReflectionClass($entry['class']);
        $service = $reflector->newInstanceArgs($arguments);
        unset($reflector);

        return $service;
    }

    /**
     * Resolve argument definitions into an array of arguments.
     * @param array  $argumentDefinitions The service arguments definition.
     * @return array The service constructor arguments.
     */
    private function resolveArguments(array $argumentDefinitions)
    {
        $arguments = [];

        foreach ($argumentDefinitions as $argumentDefinition) {
            if ($argumentDefinition instanceof ServiceReference) {
                $argumentServiceName = $argumentDefinition->getName();

                $arguments[] = $this->get($argumentServiceName);
            } elseif ($argumentDefinition instanceof ParameterReference) {
                $argumentParameterName = $argumentDefinition->getName();

                $arguments[] = $this->getParameter($argumentParameterName);
            } else {
                $arguments[] = $argumentDefinition;
            }
        }

        return $arguments;
    }

    /**
     * Initialize a service using the call definitions.
     *
     * @param object $service         The service.
     * @param string $name            The service name.
     * @param array  $callDefinitions The service calls definition.
     *
     * @throws ContainerException On failure.
     */
    private function initializeService($service, $name, array $callDefinitions)
    {
        foreach ($callDefinitions as $callDefinition) {
            if (!is_array($callDefinition) || !isset($callDefinition['method'])) {
                throw new ContainerException($name.' service calls must be arrays containing a \'method\' key');
            } elseif (!is_callable([$service, $callDefinition['method']])) {
                throw new ContainerException($name.' service asks for call to uncallable method: '.$callDefinition['method']);
            }

            $arguments = isset($callDefinition['arguments']) ? $this->resolveArguments($callDefinition['arguments']) : [];
            $arguments = $this->settingsParameters($arguments);
            call_user_func_array([$service, $callDefinition['method']], $arguments);
        }
    }

    private function settingsParameters($parameter)
    {

        if (is_array($parameter)) {
            foreach ($parameter as $k=>$item) {
                $parameter[$k] = $this->settingsParameters($item);
            }
        }

        if (is_string($parameter) && preg_match_all('~{%.+?%}~', $parameter, $matches)) {
            $settings = $this->get(Settings::class);
            $matches = $matches[0];
            foreach ($matches as $match) {
                $var = preg_replace('~{%(.+)?%}~', '$1', $match);

                if (!empty($param = $settings->$var)) {
                    if (is_array($param) || is_object($param)) {
                        $parameter = $param;
                    } else {
                        $parameter = strtr($parameter, [$match => $param]);
                    }
                } else {
                    $parameter = strtr($parameter, [$match => '']);
                }
            }
        }

        return $parameter;
    }

    private function configParameters($parameter)
    {

        if (is_array($parameter)) {
            foreach ($parameter as $k=>$item) {
                $parameter[$k] = $this->configParameters($item);
            }
        }

        if (is_string($parameter) && preg_match_all('~\{\$.+?\}~', $parameter, $matches)) {
            $config = $this->get(Config::class);
            $matches = $matches[0];
            foreach ($matches as $match) {
                $var = preg_replace('~\{\$(.+)?\}~', '$1', $match);

                if (!empty($param = $config->$var)) {
                    $parameter = strtr($parameter, [$match => $param]);
                } else {
                    $parameter = strtr($parameter, [$match => '']);
                }
            }
        }

        return $parameter;
    }
    
}
