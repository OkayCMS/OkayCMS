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

session_name('okay_admin_sid');

session_start();
require_once('vendor/autoload.php');

$DI = include 'Okay/Core/config/container.php';

/** @var Config $config */
$config = $DI->get(Config::class);

if ($config->get('debug_mode') == true) {
    ini_set('display_errors', 'on');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'off');
    error_reporting(0);
}

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

if (!is_object($manager) || empty($manager->id)) {
    print "not admin :(";
    exit;
}
/** @var object{id: int|string, lang?: mixed} $manager */

$design->setTemplatesDir('backend/design/js/admintooltip');
$design->setCompiledDir('backend/design/compiled');

// Перевод админки
$backendTranslations = $DI->get(BackendTranslations::class);
$managerLang = isset($manager->lang) && is_scalar($manager->lang) ? (string)$manager->lang : '';
$backendTranslations->initTranslations($managerLang);
$design->assign('btr', $backendTranslations);
$language = $DI->get(EntityFactory::class)->get(LanguagesEntity::class)->get($managerLang);
$design->assign('language', $language);
$frontLangId = $_SESSION['lang_id'] ?? (is_object($language) && isset($language->id) ? (string)$language->id : $managerLang);
$design->assign('front_lang_id', $frontLangId);

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
