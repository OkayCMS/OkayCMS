<?php

use Okay\Core\Response;
use Okay\Core\EntityFactory;
use Okay\Core\Managers;
use Okay\Core\Security\BackendFileDownloadPolicy;
use Okay\Core\Security\AdminSession;
use Okay\Entities\ManagersEntity;
use Okay\Core\Modules\Modules;

chdir('../..');

require_once('vendor/autoload.php');

session_name(AdminSession::SESSION_NAME);
AdminSession::configureCookieParams($_SERVER);
session_start();

$DI = include 'Okay/Core/config/container.php';

/** @var Modules $modules */
$modules = $DI->get(Modules::class);
$modules->startEnabledModules();

$modules->registerSmartyPlugins();

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var EntityFactory $entityFactory */
$entityFactory = $DI->get(EntityFactory::class);

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);
$manager = !empty($_SESSION['admin']) ? $managersEntity->get($_SESSION['admin']) : null;

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

if (empty($manager)) {
    exit();
}

$file = preg_replace("/[^A-Za-z0-9_]+/", "", (string)($_GET['file'] ?? ''));
$folder = preg_replace("/[^A-Za-z0-9_]+/", "", (string)($_GET['folder'] ?? ''));
$ext = strtolower(preg_replace("/[^A-Za-z0-9]+/", "", (string)($_GET['ext'] ?? '')) ?? '');
if (empty($file) || empty($folder) || empty($ext)) {
    exit();
}

$downloadPolicy = new BackendFileDownloadPolicy();
$permission = $downloadPolicy->permissionFor($folder, $file, $ext);
if (!$permission || !$managers->access($permission, $manager)) {
    exit();
}

$allowedExtensions = [
    'image' => ['png', 'jpg', 'jpeg', 'gif', 'tif', 'bmp', 'ico'],
];

$filePath = __DIR__ . '/' . $folder . '/' . $file . '.' . $ext;
if (!is_file($filePath)) {
    exit();
}

if ($ext == 'csv') {
    $response->addHeader('Content-Description: File Transfer');
    $response->addHeader('Content-Type: application/octet-stream');
    $response->addHeader('Content-Disposition: attachment; filename=' . basename($filePath));
    $response->addHeader('Expires: 0');
    $response->addHeader('Cache-Control: must-revalidate');
    $response->addHeader('Pragma: public');
    $response->addHeader('Content-Length: ' . filesize($filePath));
    $response->addHeader('Content-Description: File Transfer');
    $response->sendHeaders();
    readfile($filePath);
    exit();
} elseif (in_array($ext, $allowedExtensions['image'], true)) {
    $response->setContent(file_get_contents($filePath), RESPONSE_IMAGE);
    $response->sendContent();
}

exit();
