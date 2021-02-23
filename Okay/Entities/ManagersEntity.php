<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class ManagersEntity extends Entity
{

    protected static $fields = [
        'id',
        'lang',
        'login',
        'email',
        'password',
        'permissions',
        'cnt_try',
        'last_try',
        'comment',
        'menu_status',
        'menu',
        'last_activity',
    ];

    protected static $defaultOrderFields = [
        'id ASC',
    ];

    protected static $table = '__managers';
    protected static $tableAlias = 'm';
    protected static $alternativeIdField = 'login';

    public function find(array $filter = [])
    {
        $managerCore = $this->serviceLocator->getService(\Okay\Core\Managers::class);
        $managers = parent::find($filter);
        foreach ($managers as $m) {
            $managerCore->setManagerPermissions($m);
            if (!empty($m->menu)) {
                $m->menu = unserialize($m->menu);
            } else {
                $m->menu = array();
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $managers, func_get_args());
    }

    public function get($id)
    {
        $managerCore = $this->serviceLocator->getService(\Okay\Core\Managers::class);
        if ($manager = parent::get($id)) {
            $manager->menu = unserialize($manager->menu);

            $managerCore->setManagerPermissions($manager);
            return ExtenderFacade::execute([static::class, __FUNCTION__], $manager, func_get_args());
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /*Добавление менеджера*/
    public function add($manager)
    {

        /** @var \Okay\Core\Managers $managersCore */
        $managersCore = $this->serviceLocator->getService(\Okay\Core\Managers::class);
        
        $manager = (object)$manager;
        if (!empty($manager->password)) {
            // захешировать пароль
            $manager->password = $managersCore->cryptApr1Md5($manager->password);
        }

        if (!empty($manager->menu) && is_array($manager->menu)) {
            $manager->menu = serialize($manager->menu);
        }

        if (isset($manager->permissions) && is_array($manager->permissions)) {
            if (count(array_diff($managersCore->getAllPermissions(), $manager->permissions))>0) {
                $manager->permissions = implode(",", array_intersect($managersCore->getAllPermissions(), $manager->permissions));
            } else {
                // все права
                $manager->permissions = null;
            }
        }
        
        return parent::add($manager);
    }

    /*Обновление менеджеров*/
    public function update($id, $manager)
    {
        /** @var \Okay\Core\Managers $managersCore */
        $managersCore = $this->serviceLocator->getService(\Okay\Core\Managers::class);
        
        $manager = (object)$manager;
        if (!empty($manager->password)) {
            // захешировать пароль
            $manager->password = $managersCore->cryptApr1Md5($manager->password);
        }

        if (!empty($manager->menu) && is_array($manager->menu)) {
            $manager->menu = serialize($manager->menu);
        }
        
        if (isset($manager->permissions) && is_array($manager->permissions)) {
            if (count(array_diff($managersCore->getAllPermissions(), $manager->permissions))>0) {
                $manager->permissions = implode(",", array_intersect($managersCore->getAllPermissions(), $manager->permissions));
            } else {
                // все права
                $manager->permissions = null;
            }
        }

        return parent::update($id, $manager);
    }

    public function updateLastActivityDate($managerId)
    {
        $update = $this->queryFactory->newUpdate();
        $update->set('last_activity', 'NOW()')
            ->table(self::getTable())
            ->where( 'id=:id')
            ->bindValue('id', $managerId);
        $this->db->query($update);

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }
}
