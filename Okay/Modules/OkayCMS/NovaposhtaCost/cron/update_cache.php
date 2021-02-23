<?php

use Okay\Core\Modules\Modules;
use Okay\Modules\OkayCMS\NovaposhtaCost\NovaposhtaCost;

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

require_once('vendor/autoload.php');

$DI = include 'Okay/Core/config/container.php';

/** @var Modules $modules */
$modules = $DI->get(Modules::class);
$modules->startEnabledModules();

/** @var NovaposhtaCost $novaposhtaCost */
$novaposhtaCost = $DI->get(NovaposhtaCost::class);

$novaposhtaCost->parseCitiesToCache();
$novaposhtaCost->parseWarehousesToCache();