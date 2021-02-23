<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\UserGroupsEntity;
use Okay\Entities\UsersEntity;

class BackendUserGroupsHelper
{
    /**
     * @var UserGroupsEntity
     */
    private $userGroupsEntity;
    
    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        Request       $request
    ) {
        $this->entityFactory = $entityFactory;
        $this->userGroupsEntity  = $entityFactory->get(UserGroupsEntity::class);
        $this->request      = $request;
    }

    public function findGroups($filter)
    {
        $usersEntity = $this->entityFactory->get(UsersEntity::class);
        $groups = $this->userGroupsEntity->mappedBy('id')->find($filter);
        foreach ($groups as $group) {
            $group->cnt_users = $usersEntity->count(["group_id"=>$group->id]);
        }
        
        return ExtenderFacade::execute(__METHOD__, $groups, func_get_args());
    }

    public function prepareAdd($group)
    {
        return ExtenderFacade::execute(__METHOD__, $group, func_get_args());
    }

    public function add($group)
    {
        $insertId = $this->userGroupsEntity->add($group);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }
    
    public function prepareUpdate($group)
    {
        return ExtenderFacade::execute(__METHOD__, $group, func_get_args());
    }

    public function update($id, $group)
    {
        $this->userGroupsEntity->update($id, $group);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getGroup($id)
    {
        $group = $this->userGroupsEntity->get($id);
        return ExtenderFacade::execute(__METHOD__, $group, func_get_args());
    }

    public function delete($ids)
    {
        $this->userGroupsEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

}