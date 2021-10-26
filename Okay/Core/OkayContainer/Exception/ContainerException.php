<?php

namespace Okay\Core\OkayContainer\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Container exceptions are thrown by the container when it cannot behave as it
 * has been requested to.
 */
class ContainerException extends \Exception implements ContainerExceptionInterface {}
