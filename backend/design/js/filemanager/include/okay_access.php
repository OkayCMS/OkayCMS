<?php

use Okay\Core\EntityFactory;
use Okay\Core\Managers;
use Okay\Entities\ManagersEntity;

$okayRootDir = dirname(__DIR__, 5);
$okayFilemanagerDir = dirname(__DIR__);

if (session_status() === PHP_SESSION_NONE) {
    session_name('okay_admin_sid');
    session_start();
}

$previousDirectory = getcwd();
chdir($okayRootDir);

require_once $okayRootDir . '/vendor/autoload.php';

$DI = include $okayRootDir . '/Okay/Core/config/container.php';

/** @var EntityFactory $entityFactory */
$entityFactory = $DI->get(EntityFactory::class);

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

$manager = !empty($_SESSION['admin']) ? $managersEntity->get($_SESSION['admin']) : null;

chdir($okayFilemanagerDir);

if (empty($manager) || !$managers->access('images', $manager)) {
    http_response_code(403);
    exit('Access Denied');
}
