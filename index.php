<?php

$startTime = microtime(true);

use Okay\Core\Router;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Config;
use Okay\Core\DebugBar\DebugBar;
use Okay\Core\Modules\Modules;
use Okay\Core\OkayContainer\OkayContainer;
use Psr\Log\LoggerInterface;

ini_set('display_errors', 'off');

require_once('vendor/autoload.php');

if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    session_name(md5($_SERVER['HTTP_USER_AGENT']));
}
session_start();

/** @var OkayContainer $DI */
$DI = include 'Okay/Core/config/container.php';

/** Инициализируем панель отладки */
if (false) {
    DebugBar::init();
}
DebugBar::startMeasure('init', 'System init');

/** @var Config $config Конфигурируем в конструкторе сервиса параметры системы */
$config = $DI->get(Config::class);

try {
    /** @var Router $router */
    $router = $DI->get(Router::class);
    
    // Редирект с повторяющихся слешей
    $uri = str_replace(Request::getDomainWithProtocol(), '', Request::getCurrentUrl());
    if (($destination = preg_replace('~//+~', '/', $uri, -1, $countReplace)) && $countReplace > 0) {
        Response::redirectTo($destination, 301);
    }
    $router->resolveCurrentLanguage();

    if ($config->get('debug_mode') == true) {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
    }
    
    /** @var Response $response */
    $response = $DI->get(Response::class);
    
    /** @var Request $request */
    $request = $DI->get(Request::class);
    // Установим время начала выполнения скрипта
    $request->setStartTime($startTime);

    if (isset($_GET['logout'])) {
        unset($_SESSION['admin']);
        unset($_SESSION['modules_request_timeout']);
        unset($_SESSION['support_request_timeout']);
        unset($_SESSION['last_version_data']);
        setcookie('admin_login', '', time()-100, '/');
        
        $response->redirectTo($request->getRootUrl());
    }
    
    /** @var Modules $modules */
    $modules = $DI->get(Modules::class);
    DebugBar::stopMeasure('init');
    $modules->startEnabledModules();

    $router->run();

    if ($response->getContentType() == RESPONSE_HTML) {
        // Отладочная информация
        print "<!--\r\n";
        $timeEnd = microtime(true);
        $execTime = $timeEnd - $startTime;

        if (function_exists('memory_get_peak_usage')) {
            print "memory peak usage: " . memory_get_peak_usage() . " bytes\r\n";
        }
        print "page generation time: " . $execTime . " seconds\r\n";
        print "-->";
    }

} catch (\Exception $e) {
    
    /** @var LoggerInterface $logger */
    $logger = $DI->get(LoggerInterface::class);
    
    $message = $e->getMessage() . PHP_EOL . $e->getTraceAsString();
    header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error');
    if ($config->get('debug_mode') == true) {
        print $message;
    } else {
        $logger->critical($message);
    }
}
