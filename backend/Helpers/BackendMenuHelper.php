<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\MenuEntity;
use Okay\Entities\MenuItemsEntity;

class BackendMenuHelper
{
    
    private $entityFactory;

    /** @var MenuEntity */
    private $menuEntity;
    
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
        $this->menuEntity = $entityFactory->get(MenuEntity::class);
    }

    public function buildFilter()
    {
        $filter = [];
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
    
    public function getMenu($id)
    {
        $menu = $this->menuEntity->findOne(['id' => $id]);
        return ExtenderFacade::execute(__METHOD__, $menu, func_get_args());
    }

    public function findMenus($filter)
    {
        $menus = $this->menuEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $menus, func_get_args());
    }

    public function enable($ids)
    {
        $this->menuEntity->update($ids, ['visible' => 1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($ids)
    {
        $this->menuEntity->update($ids, ['visible' => 0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->menuEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function defaultAction($action, $ids)
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function prepareAdd($menu)
    {
        return ExtenderFacade::execute(__METHOD__, $menu, func_get_args());
    }

    public function add($menu)
    {
        $insertId = $this->menuEntity->add($menu);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($menu)
    {
        return ExtenderFacade::execute(__METHOD__, $menu, func_get_args());
    }
    
    public function update($menu)
    {
        if (empty($menu->id)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        
        $this->menuEntity->update($menu->id, $menu);
        
        return ExtenderFacade::execute(__METHOD__, $menu, func_get_args());
    }

    public function prepareAddMenuItem($item)
    {
        return ExtenderFacade::execute(__METHOD__, $item, func_get_args());
    }

    public function addMenuItem($item)
    {
        /** @var MenuItemsEntity $menuItemsEntity */
        $menuItemsEntity = $this->entityFactory->get(MenuItemsEntity::class);
        $insertId = $menuItemsEntity->add($item);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdateMenuItem($item)
    {
        return ExtenderFacade::execute(__METHOD__, $item, func_get_args());
    }
    
    public function updateMenuItem($item)
    {
        if (empty($item->id)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        /** @var MenuItemsEntity $menuItemsEntity */
        $menuItemsEntity = $this->entityFactory->get(MenuItemsEntity::class);
        $menuItemsEntity->update($item->id, $item);
        
        return ExtenderFacade::execute(__METHOD__, $item, func_get_args());
    }
    
    public function getMenuItemsTree($menuId)
    {
        /** @var MenuItemsEntity $menuItemsEntity */
        $menuItemsEntity = $this->entityFactory->get(MenuItemsEntity::class);
        $menuItemsTree = $menuItemsEntity->getMenuItemsTree((int)$menuId);
        return ExtenderFacade::execute(__METHOD__, $menuItemsTree, func_get_args());
    }
    
    public function buildTree($items)
    {
        $tree = new \stdClass();
        $tree->submenus = [];

        // Указатели на узлы дерева
        $pointers = [];
        $pointers[0] = &$tree;

        $finish = false;
        // Не кончаем, пока не кончатся элементы, или пока ни одну из оставшихся некуда приткнуть
        while (!empty($items) && !$finish) {
            $flag = false;
            // Проходим все выбранные элементы
            foreach ($items as $k => $item) {
                if (isset($pointers[$item->parent_index])) {
                    // В дерево элементов меню (через указатель) добавляем текущий элемент
                    $pointers[$item->index] = $pointers[$item->parent_index]->submenus[] = $item;

                    // Убираем использованный элемент из массива
                    unset($items[$k]);
                    $flag = true;
                }
            }
            if (!$flag) $finish = true;
        }
        unset($pointers[0]);
        
        return ExtenderFacade::execute(__METHOD__, $tree->submenus, func_get_args());
    }
}
