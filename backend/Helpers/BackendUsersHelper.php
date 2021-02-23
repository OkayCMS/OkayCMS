<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\OrdersEntity;
use Okay\Entities\UserGroupsEntity;
use Okay\Entities\UsersEntity;

class BackendUsersHelper
{
    /**
     * @var UsersEntity
     */
    private $usersEntity;
    
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
    ){
        $this->entityFactory = $entityFactory;
        $this->usersEntity  = $entityFactory->get(UsersEntity::class);
        $this->request      = $request;
    }

    public function findUsers($filter, $usersSort = null)
    {
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        
        if (!empty($usersSort)) {
            $this->usersEntity->order($usersSort);
        }
        $users = $this->usersEntity->mappedBy('id')->find($filter);

        foreach ($users as $user) {
            $user->orders = $ordersEntity->find(['user_id'=>$user->id]);
        }
        
        return ExtenderFacade::execute(__METHOD__, $users, func_get_args());
    }

    public function getAllGroups()
    {
        /** @var UserGroupsEntity $userGroupsEntity */
        $userGroupsEntity = $this->entityFactory->get(UserGroupsEntity::class);
        $groups = $userGroupsEntity->mappedBy('id')->find();
        return ExtenderFacade::execute(__METHOD__, $groups, func_get_args());
    }
    
    public function getUsersSort()
    {
        $usersSort = null;
        // Сортировка пользователей, сохраняем в сессии, чтобы текущая сортировка не сбрасывалась
        if ($sort = $this->request->get('sort', 'string')) {
            $_SESSION['users_admin_sort'] = $sort;
        }
        if (!empty($_SESSION['users_admin_sort'])) {
            $usersSort = $_SESSION['users_admin_sort'];
        } else {
            $usersSort = 'name';
        }
        return ExtenderFacade::execute(__METHOD__, $usersSort, func_get_args());
    }

    public function prepareUpdate($user)
    {
        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }

    public function update($id, $user)
    {
        $this->usersEntity->update($id, $user);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getUser($id)
    {
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        $user = $this->usersEntity->get($id);
        $user->orders = $ordersEntity->find(['user_id'=>$user->id]);
        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }
    

    public function delete($ids)
    {
        $this->usersEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function moveToGroup($ids, $targetGroupId)
    {
        if (!empty($targetGroupId)) {
            $this->usersEntity->update($ids, ['group_id' => $targetGroupId]);
        }
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;

        $groupId = $this->request->get('group_id', 'integer');
        if (!empty($groupId)) {
            $filter['group_id'] = $groupId;
        }

        // Поиск
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $this->usersEntity->count($filter);
        }
        
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function countUsers($filter)
    {
        $count = $this->usersEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

}