<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendMenuHelper;
use Okay\Admin\Requests\BackendMenuRequest;
use Okay\Entities\MenuEntity;
use Okay\Entities\MenuItemsEntity;

class MenuAdmin extends IndexAdmin
{

    public function fetch(
        BackendMenuRequest $backendMenuRequest,
        BackendMenuHelper $backendMenuHelper,
        MenuEntity $menuEntity,
        MenuItemsEntity $menuItemsEntity
    ) {

        $menuItems = [];
        
        /*Принимаем данные о меню*/
        if ($this->request->method('POST')) {
            $menu = $backendMenuRequest->postMenu();
            $menuItems = $backendMenuRequest->postMenuItems();

            if (($m = $menuEntity->get((string)$menu->group_id)) && $m->id!=$menu->id) {
                $this->design->assign('message_error', 'group_id_exists');
                $menuItems = $backendMenuHelper->buildTree($menuItems);
            } elseif (empty($menu->group_id)) {
                $this->design->assign('message_error', 'empty_group_id');
                $menuItems = $backendMenuHelper->buildTree($menuItems);
            } else {
                /*Добавляем/обновляем меню*/
                if (empty($menu->id)) {
                    $preparedMenu = $backendMenuHelper->prepareAdd($menu);
                    $menu->id  = $backendMenuHelper->add($preparedMenu);

                    $this->design->assign('message_success', 'added');
                } else {
                    $preparedMenu = $backendMenuHelper->prepareUpdate($menu);
                    $backendMenuHelper->update($preparedMenu);
                    
                    $this->design->assign('message_success', 'updated');
                }
                
                if ($menu->id) {
                    $menuItemsIds = [];
                    if (is_array($menuItems)) {
                        foreach ($menuItems as $i=>$item) {
                            if ($item->parent_index > 0) {
                                if (!isset($menuItems[$item->parent_index]->id)) {
                                    unset($menuItems[$i]);
                                    continue;
                                }
                                $item->parent_id = $menuItems[$item->parent_index]->id;
                            } else {
                                $item->parent_id = 0;
                            }

                            $item->menu_id = $menu->id;
                            unset($item->index);
                            unset($item->parent_index);
                            unset($item->i_tm);
                            if (empty($item->id)) {
                                $preparedMenuItem = $backendMenuHelper->prepareAddMenuItem($item);
                                $item->id  = $backendMenuHelper->addMenuItem($preparedMenuItem);
                            } else {
                                $preparedMenuItem = $backendMenuHelper->prepareUpdateMenuItem($item);
                                $backendMenuHelper->updateMenuItem($preparedMenuItem);
                            }
                            if ($item->id) {
                                $menuItemsIds[] = $item->id;
                            }
                        }
                    }

                    // удаляем не переданные элементы меню
                    $currentMenuItemsIds = $menuItemsEntity->cols(['id'])->find(['menu_id' => $menu->id]);
                    foreach ($currentMenuItemsIds as $menuItemId) {
                        if (!in_array($menuItemId, $menuItemsIds)) {
                            $menuItemsEntity->delete($menuItemId);
                        }
                    }

                    // Отсортировать  элементы меню
                    asort($menuItemsIds);
                    $i = 0;
                    foreach($menuItemsIds as $menu_item_id) {
                        $menuItemsEntity->update($menuItemsIds[$i], ['position'=>$menu_item_id]);
                        $i++;
                    }

                    $menuItems = $backendMenuHelper->getMenuItemsTree((int)$menu->id);
                }
                $menu = $backendMenuHelper->getMenu($menu->id);
            }
        } else {
            /*Отображение меню*/
            $id = $this->request->get('id', 'integer');
            if (!empty($id)) {
                $menu = $backendMenuHelper->getMenu($id);
                if (!empty($menu->id)) {
                    $menuItems = $backendMenuHelper->getMenuItemsTree((int)$menu->id);
                }
            } else {
                $menu = new \stdClass();
                $menu->visible = 1;
            }
        }

        $this->design->assign('menu', $menu);
        $this->design->assign('menu_items', $menuItems);
        $this->response->setContent($this->design->fetch('menu.tpl'));
    }
}
