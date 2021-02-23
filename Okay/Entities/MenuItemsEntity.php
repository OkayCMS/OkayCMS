<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class MenuItemsEntity extends Entity
{

    // Список указателей на элементы меню в дереве элементов меню (ключ = id элемента меню)
    private $allMenuItems;
    // Дерево элементов меню
    private $menuItemsTree;

    protected static $fields = [
        'id',
        'menu_id',
        'parent_id',
        'url',
        'is_target_blank',
        'visible',
        'position',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $defaultOrderFields = [
        'parent_id',
        'position',
    ];

    protected static $table = '__menu_items';
    protected static $langObject = 'menu_item';
    protected static $langTable = 'menu_items';
    protected static $tableAlias = 'it';

    public function getMenuItemsTree($menuId = 0)
    {
        if (!isset($this->menuItemsTree)) {
            $this->initMenuItems();
        }

        if ($menuId <= 0) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], $this->menuItemsTree, func_get_args());
        }

        if (!isset($this->menuItemsTree[$menuId])) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->menuItemsTree[$menuId], func_get_args());
    }

    public function getMenuItems()
    {
        if (!isset($this->menuItemsTree)) {
            $this->initMenuItems();
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->allMenuItems, func_get_args());
    }

    public function add($menuItem)
    {
        $id = parent::add($menuItem);
        unset($this->menuItemsTree);
        unset($this->allMenuItems);
        return $id;
    }

    public function update($id, $menuItem)
    {
        $id = parent::update($id, $menuItem);
        unset($this->menuItemsTree);
        unset($this->allMenuItems);
        return (int)$id;
    }

    private function initMenuItems()
    {
        $menuItems = $this->menuItemsTree = $this->allMenuItems = [];
        
        $items = $this->find();
        
        foreach ($items as $item) {
            if (!isset($menuItems[$item->menu_id]))  {
                $menuItems[$item->menu_id] = [];
            }
            $menuItems[$item->menu_id][] = $item;
        }

        foreach ($menuItems as $menu_id => $items) {
            // Дерево элементов меню
            $tree = new \stdClass();
            $tree->submenus = array();

            // Указатели на узлы дерева
            $pointers = array();
            $pointers[0] = &$tree;
            $pointers[0]->path = array();
            $pointers[0]->level = 0;

            $finish = false;
            // Не кончаем, пока не кончатся элементы, или пока ниодну из оставшихся некуда приткнуть
            while (!empty($items) && !$finish) {
                $flag = false;
                // Проходим все выбранные элементы
                foreach ($items as $k => $item) {
                    if (isset($pointers[$item->parent_id])) {
                        // В дерево элементов меню (через указатель) добавляем текущий элемент
                        $pointers[$item->id] = $pointers[$item->parent_id]->submenus[] = $item;

                        // Путь к текущему элементу
                        $curr = $pointers[$item->id];
                        $pointers[$item->id]->path = array_merge((array)$pointers[$item->parent_id]->path, array($curr));

                        // Уровень вложенности элементов
                        $pointers[$item->id]->level = 1 + $pointers[$item->parent_id]->level;

                        // Убираем использованный элемент из массива
                        unset($items[$k]);
                        $flag = true;
                    }
                }
                if (!$flag) $finish = true;
            }

            // Для каждого элемента id всех его деток узнаем
            $ids = array_reverse(array_keys($pointers));
            foreach ($ids as $id) {
                if ($id > 0) {
                    $pointers[$id]->children[] = $id;

                    if (isset($pointers[$pointers[$id]->parent_id]->children)) {
                        $pointers[$pointers[$id]->parent_id]->children = array_merge($pointers[$id]->children, $pointers[$pointers[$id]->parent_id]->children);
                    } else {
                        $pointers[$pointers[$id]->parent_id]->children = $pointers[$id]->children;
                    }
                }
            }
            unset($pointers[0]);
            unset($ids);
            $this->menuItemsTree[$menu_id] = $tree->submenus;
            $this->allMenuItems = $this->allMenuItems+$pointers;
        }

        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }
    
}
