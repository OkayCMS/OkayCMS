<?php

use Okay\Core\EntityFactory;
use Okay\Core\Config;
use Okay\Core\Response;
use Okay\Core\Design;
use Okay\Core\ManagerMenu;
use Okay\Core\BackendTranslations;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\ManagersEntity;
use Okay\Core\Modules\Modules;

chdir('../../../../');

if (!empty($_SERVER['HTTP_USER_AGENT'])){
    session_name(md5($_SERVER['HTTP_USER_AGENT']));
}

session_start();
require_once('vendor/autoload.php');

$DI = include 'Okay/Core/config/container.php';

/** @var Config $config */
$config = $DI->get(Config::class);

/** @var ManagerMenu $managerMenu */
$managerMenu = $DI->get(ManagerMenu::class);

/** @var Modules $modules */
$modules = $DI->get(Modules::class);
$modules->startEnabledModules();

$modules->registerSmartyPlugins();

// Кеширование нам не нужно
/** @var Response $response */
$response = $DI->get(Response::class);
$response->addHeader('Cache-Control: no-cache, must-revalidate');
$response->addHeader('Expires: -1');
$response->addHeader('Pragma: no-cache');

$manager = $DI->get(EntityFactory::class)->get(ManagersEntity::class)->get($_SESSION['admin']);

/** @var Design $design */
$design = $DI->get(Design::class);

if (empty($manager->id)) {
    print "not admin :(";
    exit;
}

$design->setTemplatesDir('backend/design/js/admintooltip');
$design->setCompiledDir('backend/design/compiled');

// Перевод админки
$backendTranslations = $DI->get(BackendTranslations::class);
$backendTranslations->initTranslations($manager->lang);
$design->assign('btr', $backendTranslations);
$language = $manager = $DI->get(EntityFactory::class)->get(LanguagesEntity::class)->get((string)$manager->lang);
$design->assign('language', $language);

$menuSelector = [];
$fastMenu = $managerMenu->getFastMenu();
foreach ($fastMenu as $dataProperty => $menuItem) {
    $menuSelector[] = '[data-' . $dataProperty . ']';
}

$design->assign('menu_selector', '"' . implode(', ', $menuSelector) . '"');
$design->assign('fast_menu', $fastMenu);

$response->addHeader('Content-Type: application/javascript');
$response->setContent($design->fetch('tooltip.tpl'), RESPONSE_JAVASCRIPT);
$response->sendContent();