<?php


namespace Okay\Modules\OkayCMS\Integration1C\Integration;


use Aura\SqlQuery\QueryFactory;
use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\Settings;

abstract class AbstractFactory
{

    /** @var Integration1C */
    protected $integration1C;

    public function __construct(Integration1C $integration1C)
    {
        $this->integration1C = $integration1C;
    }
    
    abstract public function create($type);
}