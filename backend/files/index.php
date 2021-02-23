<?php

use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\EntityFactory;
use Okay\Entities\ManagersEntity;
use Okay\Core\Modules\Modules;

if(!empty($_SERVER['HTTP_USER_AGENT'])){
    session_name(md5($_SERVER['HTTP_USER_AGENT']));
}
session_start();

chdir('../..');

require_once('vendor/autoload.php');

$DI = include 'Okay/Core/config/container.php';

/** @var Modules $modules */
$modules = $DI->get(Modules::class);
$modules->startEnabledModules();

$modules->registerSmartyPlugins();

/** @var Request $request */
$request = $DI->get(Request::class);

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var EntityFactory $entityFactory */
$entityFactory = $DI->get(EntityFactory::class);

/** @var EntityFactory $entityFactory */
$entityFactory = $DI->get(EntityFactory::class);

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);
$manager = $managersEntity->get($_SESSION['admin']);

if (empty($manager)) {
    exit();
}

$file = $_GET['file'];
$file = preg_replace("/[^A-Za-z0-9_]+/", "", $file);
$folder = $_GET['folder'];
$ext = $_GET['ext'];
if (empty($file) || empty($folder) || empty($ext)) {
    exit();
}

$file = __DIR__.'/'.$folder.'/'.$file.'.'.$ext;
if (!file_exists($file)) {
    exit();
}

if ($ext == 'csv') {
    $response->addHeader('Content-Description: File Transfer');
    $response->addHeader('Content-Type: application/octet-stream');
    $response->addHeader('Content-Disposition: attachment; filename='.basename($file));
    $response->addHeader('Expires: 0');
    $response->addHeader('Cache-Control: must-revalidate');
    $response->addHeader('Pragma: public');
    $response->addHeader('Content-Length: ' . filesize($file));
    $response->addHeader('Content-Description: File Transfer');
    $response->sendHeaders();
    readfile($file);
    exit();
} elseif ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'tif' || $ext == 'bmp' || $ext == 'bmp') {
    $response->setContent(file_get_contents($file), RESPONSE_IMAGE);
    $response->sendContent();
}

exit();