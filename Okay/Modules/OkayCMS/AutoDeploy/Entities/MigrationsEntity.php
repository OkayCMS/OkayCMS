<?php


namespace Okay\Modules\OkayCMS\AutoDeploy\Entities;


use Okay\Core\Entity\Entity;

class MigrationsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
    ];

    protected static $defaultOrderFields = [
        'id',
    ];

    protected static $table = 'okaycms__migrations';
    protected static $tableAlias = 'mi';
    
}
