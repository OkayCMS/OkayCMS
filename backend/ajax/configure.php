<?php

use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Settings;
use Okay\Core\Config;
use Okay\Core\Managers;
use Okay\Core\Security\AdminSession;
use Okay\Entities\ManagersEntity;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Modules;
use Okay\Core\BackendTranslations;

//ini_set('display_errors', 'on');
//error_reporting(E_ALL);

chdir(dirname(dirname(__DIR__)));
require_once('vendor/autoload.php');

session_name(AdminSession::SESSION_NAME);
AdminSession::configureCookieParams($_SERVER);
session_start();

$DI = include 'Okay/Core/config/container.php';

/** @var Config $config */
$config = $DI->get(Config::class);

$smartyPlugins = include_once 'Okay/Core/SmartyPlugins/SmartyPlugins.php';

/** @var Modules $modules */
$modules = $DI->get(Modules::class);
$modules->startEnabledModules();
$modules->registerSmartyPlugins();

/** @var BackendTranslations $backendTranslations */
$backendTranslations = $DI->get(BackendTranslations::class);

/** @var EntityFactory $entityFactory */
$entityFactory = $DI->get(EntityFactory::class);

/** @var Request $request */
$request = $DI->get(Request::class);

/** @var Response $response */
$response = $DI->get(Response::class);

if (!$request->checkSession()) {
    $response->setStatusCode(403);
    $response->setContent(json_encode(false), RESPONSE_JSON);
    $response->sendContent();
    exit;
}

/** @var Settings $settings */
$settings = $DI->get(Settings::class);

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);

$manager = !empty($_SESSION['admin']) ? $managersEntity->get($_SESSION['admin']) : null;

if (!$manager) {
    trigger_error('Need to login', E_USER_ERROR); // todo 403
}

$backendTranslations->initTranslations($manager->lang);
