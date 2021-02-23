<?php
if(!empty($_SERVER['HTTP_USER_AGENT'])){
    session_name(md5($_SERVER['HTTP_USER_AGENT']));
}

//ini_set('display_errors', 'on');
//error_reporting(E_ALL);

session_start();
chdir(dirname(dirname(__DIR__)));

use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Settings;
use Okay\Core\Config;
use Okay\Core\Managers;
use Okay\Entities\ManagersEntity;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Modules;
use Okay\Core\BackendTranslations;

require_once('vendor/autoload.php');
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

/** @var Settings $settings */
$settings = $DI->get(Settings::class);

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);

$manager = $managersEntity->get($_SESSION['admin']);

$backendTranslations->initTranslations($manager->lang);

if (!$manager) {
    trigger_error('Need to login', E_USER_ERROR); // todo 403
}