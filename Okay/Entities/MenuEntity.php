<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class MenuEntity extends Entity
{

    const MENU_VAR_PREFIX = "menu_";

    protected static $fields = [
        'id',
        'group_id',
        'name',
        'visible',
        'position',
    ];

    protected static $defaultOrderFields = [
        'position ASC',
    ];

    protected static $table = '__menu';
    protected static $tableAlias = 'm';
    protected static $alternativeIdField = 'group_id';
    protected static $langTable;
    protected static $langObject;

    public function find(array $filter = [])
    {
        $menus = parent::find($filter);
        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $menu->var = '{$'.self::MENU_VAR_PREFIX.$menu->group_id."}";
            }
        }
        return $menus;
    }

    public function get($id)
    {
        if (empty($id)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $menu = parent::get($id);
        if (!empty($menu)) {
            $menu->var = '{$'.self::MENU_VAR_PREFIX.$menu->group_id."}";
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $menu, func_get_args());
    }

    public function delete($ids)
    {
        /** @var MenuItemsEntity $menuItemsEntity */
        $menuItemsEntity = $this->entity->get(MenuItemsEntity::class);

        $ids = (array)$ids;

        if (empty($ids)) {
            return false;
        }

        $menuItemsIds = $menuItemsEntity->cols(['id'])->find(['menu_id' => $ids]);
        if (!empty($menuItemsIds)) {
            $menuItemsEntity->delete($menuItemsIds);
        }

        return parent::delete($ids);
    }

}
