<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Entities\ManagersEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendManagersHelper
{
    /**
     * @var ManagersEntity
     */
    private $managersEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->managersEntity = $entityFactory->get(ManagersEntity::class);
    }

    public function findManagers($filter = [])
    {
        $managers = $this->managersEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $managers, func_get_args());
    }
}