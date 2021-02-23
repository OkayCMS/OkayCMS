<?php

use Okay\Core\OkayContainer\OkayContainer;

require_once __DIR__ . '/constants.php';
$services   = require_once __DIR__ . '/services.php';
$parameters = require_once __DIR__ . '/parameters.php';

return OkayContainer::getInstance($services, $parameters);
