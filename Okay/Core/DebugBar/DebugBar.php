<?php

namespace Okay\Core\DebugBar;

use Aura\Sql\ExtendedPdo;
use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DebugBar as LibDebugBar;
use Monolog\Logger;
use Okay\Core\DebugBar\DataCollectors\ConfigCollector;
use Okay\Core\DebugBar\DataCollectors\TimeDataCollector;
use Okay\Core\ServiceLocator;
use Psr\Log\LoggerInterface;

class DebugBar
{
    /** @var LibDebugBar */
    private static $debugBar;

    /** @var ServiceLocator */
    private static $serviceLocator;

    public static function init()
    {
        if (self::$debugBar === null && class_exists(LibDebugBar::class)) {
            self::$debugBar = new LibDebugBar();
            self::$serviceLocator = ServiceLocator::getInstance();

            self::initCollectors();

            /** @var Logger $logger */
            $logger = self::$serviceLocator->getService(LoggerInterface::class);
            self::addLogger($logger);
        }
    }

    private static function initCollectors()
    {
        if (!is_null(self::$debugBar)) {
            self::addCollector(new PhpInfoCollector());
            self::addCollector(new MessagesCollector());
            self::addCollector(new RequestDataCollector());
            self::addCollector(new MemoryCollector());

            self::addCollector(new TimeDataCollector());
            self::addCollector(new ConfigCollector());
            self::addCollector(new MonologCollector(null, Logger::DEBUG, true, 'system_log'));

            /** @var ExtendedPdo $extendedPdo */
            $extendedPdo = self::$serviceLocator->getService(ExtendedPdo::class);
            $traceablePdo = new TraceablePDO($extendedPdo);
            DebugBar::addCollector(new PDOCollector($traceablePdo));
        }
    }

    public static function addCollector(DataCollectorInterface $dataCollector)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar->addCollector($dataCollector);
        }
    }

    public static function getCollector($name)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar->getCollector($name);
        }
    }

    public static function stackData()
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar->stackData();
        }
    }

    public static function addLogger(Logger $logger)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['system_log']->addLogger($logger);
        }
    }

    public static function setConfigValue($name, $value, $source)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['config']->set($name, $value, $source);
        }
    }

    public static function getRenderer()
    {
        if (!is_null(self::$debugBar)) {
            return self::$debugBar->getJavascriptRenderer();
        }
        return null;
    }

    public static function startMeasure($name, $label = null, $collector = null, $aggregate = false)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['time']->startMeasure($name, $label, $collector, $aggregate);
        }
    }

    public static function stopMeasure($name, $params = [])
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['time']->stopMeasure($name, $params);
        }
    }

    public static function hasStartedMeasure($name)
    {
        if (!is_null(self::$debugBar)) {
            return self::$debugBar['time']->hasStartedMeasure($name);
        }
        return null;
    }

    public static function addMessage($message, $label = 'info')
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->addMessage($message, $label);
        }
    }

    public static function error($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->error($message);
        }
    }

    public static function emergency($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->emergency($message);
        }
    }

    public static function alert($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->alert($message);
        }
    }

    public static function critical($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->critical($message);
        }
    }

    public static function warning($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->warning($message);
        }
    }

    public static function notice($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->notice($message);
        }
    }

    public static function info($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->info($message);
        }
    }

    public static function debug($message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->debug($message);
        }
    }

    public static function log($level, $message)
    {
        if (!is_null(self::$debugBar)) {
            self::$debugBar['messages']->log($level, $message);
        }
    }

    public static function startExtensionExecution($trigger, $extension)
    {
        if (!is_null(self::$debugBar)) {
            $vendorName = preg_replace('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', '$1', $extension->class);
            $moduleName = preg_replace('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', '$2', $extension->class);

            self::startMeasure("$vendorName/$moduleName", "Module $vendorName/$moduleName", null, true);
        }
    }

    public static function finishExtensionExecution($trigger, $extension)
    {
        if (!is_null(self::$debugBar)) {
            $vendorName = preg_replace('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', '$1', $extension->class);
            $moduleName = preg_replace('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', '$2', $extension->class);

            self::stopMeasure("$vendorName/$moduleName", ['Extension' => "$trigger -> $extension->class::$extension->method"]);
        }
    }

    public static function startDesignBlockFetch($blockTplFile)
    {
        if (!is_null(self::$debugBar)) {
            $vendorName = preg_replace('~Okay/Modules/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/?.*~', '$1', $blockTplFile);
            $moduleName = preg_replace('~Okay/Modules/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/?.*~', '$2', $blockTplFile);

            self::startMeasure("$vendorName/$moduleName", "Module $vendorName/$moduleName", null, true);
        }
    }

    public static function finishDesignBlockFetch($blockName, $blockTplFile)
    {
        if (!is_null(self::$debugBar)) {
            $vendorName = preg_replace('~Okay/Modules/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/?.*~', '$1', $blockTplFile);
            $moduleName = preg_replace('~Okay/Modules/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/?.*~', '$2', $blockTplFile);

            self::stopMeasure("$vendorName/$moduleName", ['Design block' => "$blockName -> ".pathinfo($blockTplFile, PATHINFO_FILENAME)]);
        }
    }
}