<?php

use Okay\Core\OkayContainer\OkayContainer;
use Okay\Core\Modules\Modules;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\AutoDeploy\Helpers\DeployHelper;

chdir(dirname(__DIR__, 5));

require_once('vendor/autoload.php');

/** @var OkayContainer $DI */
$DI = require 'Okay/Core/config/container.php';

/** @var Modules $modules */
$modules = $DI->get(Modules::class);
$modules->startEnabledModules();

/** @var DeployHelper $deployHelper */
$deployHelper = $DI->get(DeployHelper::class);

$argv = Request::getArgv();
$action = !empty($argv[1]) ? $argv[1] : "update";

switch ($action) {
    case 'update': {
        $deployHelper->executeMigrations();
        break;
    }
    case 'create': {
        $migrationName = (!empty($argv[2]) ? $argv[2] : '');
        $deployHelper->createMigration($migrationName);
        break;
    }
    default: {
        echo "error: UNKNOWN ACTION (param1 must be: empty, 'update', 'create')".PHP_EOL;
    }
}