<?php


namespace Okay\Core;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Module;

class ManagerMenu
{
    
    /**
     * Массив системных контроллеров, которые в меню не выводятся, но на них отдельные разрешения
     *
     * @var array
     */
    private $systemControllers = [
        'left_support'       => ['SupportAdmin', 'TopicAdmin'],
        'left_license_title' => ['LicenseAdmin'],
    ];
    
    /**
     * Массив с меню админ. части (из него автоматически формируется главное меню админки)
     *
     * @var array
     */
    private $leftMenu = [
        'left_catalog' => [
            'left_products_title'        => ['ProductsAdmin', 'ProductAdmin'],
            'left_categories_title'      => ['CategoriesAdmin', 'CategoryAdmin'],
            'left_brands_title'          => ['BrandsAdmin', 'BrandAdmin'],
            'left_features_title'        => ['FeaturesAdmin', 'FeatureAdmin'],
        ],
        'left_orders' => [
            'left_orders_title'                    => ['OrdersAdmin', 'OrderAdmin'],
            'left_orders_settings_title'           => ['OrderSettingsAdmin'],
        ],
        'left_users' => [
            'left_users_title'           => ['UsersAdmin', 'UserAdmin'],
            'left_groups_title'          => ['UserGroupsAdmin', 'UserGroupAdmin'],
            'left_coupons_title'         => ['CouponsAdmin'],
            'left_subscribe_title'       => ['SubscribeMailingAdmin'],
        ],
        'left_pages' => [
            'left_pages_title'           => ['PagesAdmin', 'PageAdmin'],
            'left_menus_title'           => ['MenusAdmin', 'MenuAdmin'],
        ],
        'left_blog' => [
            'left_blog_title'            => ['BlogAdmin', 'PostAdmin'],
            'left_blog_categories_title' => ['BlogCategoriesAdmin', 'BlogCategoryAdmin'],
            'left_authors_title'         => ['AuthorsAdmin', 'AuthorAdmin'],
        ],
        'left_comments' => [
            'left_comments_title'        => ['CommentsAdmin'],
            'left_feedbacks_title'       => ['FeedbacksAdmin'],
            'left_callbacks_title'       => ['CallbacksAdmin'],
        ],
        'left_auto' => [
            'left_import_title'          => ['ImportAdmin'],
            'left_export_title'          => ['ExportAdmin'],
            'left_log_title'             => ['ImportLogAdmin'],
        ],
        'left_stats' => [
            'left_stats_title'           => ['StatsAdmin'],
            'left_products_stat_title'   => ['ReportStatsAdmin'],
            'left_categories_stat_title' => ['CategoryStatsAdmin'],
        ],
        'left_seo' => [
            'left_robots_title'          => ['RobotsAdmin'],
            'left_setting_counter_title' => ['SettingsCounterAdmin'],
            'left_seo_patterns_title'    => ['SeoPatternsAdmin'],
            'left_seo_filter_patterns_title' => ['SeoFilterPatternsAdmin'],
            'left_feature_aliases_title'     => ['FeaturesAliasesAdmin'],
            'left_setting_router_title'  => ['SettingsRouterAdmin'],
            'left_setting_indexing_title'  => ['SettingsIndexingAdmin'],
        ],
        'left_design' => [
            'left_theme_title'           => ['ThemeAdmin'],
            'left_template_title'        => ['TemplatesAdmin'],
            'left_style_title'           => ['StylesAdmin'],
            'left_script_title'          => ['ScriptsAdmin'],
            'left_images_title'          => ['ImagesAdmin'],
            'left_translations_title'    => ['TranslationsAdmin', 'TranslationAdmin'],
            'left_settings_theme_title'  => ['SettingsThemeAdmin'],
        ],
        'left_settings' => [
            'left_setting_general_title' => ['SettingsGeneralAdmin'],
            'left_setting_notify_title'  => ['SettingsNotifyAdmin'],
            'left_setting_catalog_title' => ['SettingsCatalogAdmin'],
            'left_currency_title'        => ['CurrencyAdmin'],
            'left_delivery_title'        => ['DeliveriesAdmin', 'DeliveryAdmin'],
            'left_payment_title'         => ['PaymentMethodsAdmin', 'PaymentMethodAdmin'],
            'left_managers_title'        => ['ManagersAdmin', 'ManagerAdmin'],
            'left_languages_title'       => ['LanguagesAdmin', 'LanguageAdmin'],
            'learning_title'             => ['LearningAdmin'],
            'left_system_title'          => ['SystemAdmin'],
            'left_orders_discounts_settings_title' => ['DiscountsSettingsAdmin'],
        ],
        'left_modules' => [
            'left_modules_list'          => ['ModulesAdmin'],
            'left_modules_marketplace'   => ['ModulesAdmin@marketplace'],
        ],
    ];

    /**
     * Полный список элементов меню быстрого редактирования
     * 
     * @var array 
     */
    private $fastMenu = [
        'feature' => [
            [
                'controller' => 'FeatureAdmin',
                'translation' => 'admintooltip_edit_feature',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'FeatureAdmin',
                'translation' => 'admintooltip_add_feature',
            ],
        ],
        'language' => [
            [
                'controller' => 'TranslationAdmin',
                'translation' => 'admintooltip_edit_translarion',
                'params' => [
                    'id' => 'id',
                ],
            ],
        ],
        'product' => [
            [
                'controller' => 'ProductAdmin',
                'translation' => 'admintooltip_edit_product',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'ProductAdmin',
                'translation' => 'admintooltip_add_product',
            ],
        ],
        'brand' => [
            [
                'controller' => 'BrandAdmin',
                'translation' => 'admintooltip_edit_brand',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'BrandAdmin',
                'translation' => 'admintooltip_add_brand',
            ],
        ],
        'page' => [
            [
                'controller' => 'PageAdmin',
                'translation' => 'admintooltip_edit_page',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'PageAdmin',
                'translation' => 'admintooltip_add_page',
            ],
        ],
        'author' => [
            [
                'controller' => 'AuthorAdmin',
                'translation' => 'admintooltip_edit_author',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'AuthorAdmin',
                'translation' => 'admintooltip_add_author',
            ],
        ],
        'post' => [
            [
                'controller' => 'PostAdmin',
                'translation' => 'admintooltip_edit_post',
                'params' => [
                    'id' => 'id',
                ],
            ],
        ],
        'category' => [
            [
                'controller' => 'CategoryAdmin',
                'translation' => 'admintooltip_edit_category',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'CategoryAdmin',
                'translation' => 'admintooltip_add_category',
            ],
            [
                'controller' => 'ProductAdmin',
                'translation' => 'admintooltip_add_product',
                'params' => [
                    'category_id' => 'id',
                ],
                'action' => 'add',
            ],
        ],
        'blog_category' => [
            [
                'controller' => 'BlogCategoryAdmin',
                'translation' => 'admintooltip_edit_category',
                'params' => [
                    'id' => 'id',
                ],
            ],
            [
                'controller' => 'BlogCategoryAdmin',
                'translation' => 'admintooltip_add_category',
            ],
            [
                'controller' => 'PostAdmin',
                'translation' => 'admintooltip_add_post',
                'params' => [
                    'category_id' => 'id',
                ],
                'action' => 'add',
            ],
        ],
    ];
    
    /**
     * Ссылки на изображения для дополнительных секцый меню. Представляют из себя ассоциативный массив с именем
     * секции в качестве ключа и путем к картинке относительно корня проекта
     *
     * @var array
     */
    private $additionalSectionIcons = [];

    /**
     * Список контроллеров, которые имеют собственную вкладку в меню
     *
     * @var array
     */
    private $modulesControllersHasOwnMenuItem = [];

    private $managers;
    private $module;
    
    private $menuCounters = [];
    
    public function __construct(Managers $managers, Module $module, $devMode = false)
    {
        $this->managers = $managers;
        $this->module   = $module;
        
        if ((bool)$devMode === true) {
            $this->leftMenu['left_design']['left_email_templates_debug'] = ['EmailTemplatesAdmin'];
        }
    }

    public function getFastMenu()
    {
        return ExtenderFacade::execute(__METHOD__, $this->fastMenu, func_get_args());
    }

    /**
     * Добавление элемента меню быстрого редактирования для администратора.
     *
     * @param string $dataProperty data атрибут который должен быть у html элемента, и при наведении на который будет
     * открываться данное меню
     * @param array ...$menuItems массив описаний ссылок меню
     * @throws \Exception
     * @example $this->extendBackendMenu('property', [
            'controller' => 'Vendor.Module.Controller',
            'translation' => 'translation_var_add',
        ], [
            'controller' => 'Vendor.Module.Controller',
            'translation' => 'translation_var_edit',
            'params' => [
                'id' => 'id',
            ],
            'action' => 'edit',
        ]);
     * При наведении на элемент с атрибутом data-property="1" будут построены ссылки на добавление сущности через
     * контроллер Vendor.Module.Controller и на редактирование с GET параметром id=1 (указанным в data-property).
     */
    public function addFastMenuItem($dataProperty, ...$menuItems)
    {
        $validatedMenuItems = [];
        foreach ($menuItems as $item) {
            $validatedMenuItem = [];
            if (!empty($item['controller'])) {
                $validatedMenuItem['controller'] = $item['controller'];
            } else {
                throw new \Exception('Controller is required for fastMenu');
            }
            if (!empty($item['translation'])) {
                $validatedMenuItem['translation'] = $item['translation'];
            } else {
                throw new \Exception('Translation is required for fastMenu');
            }
            if (!empty($item['params'])) {
                $validatedMenuItem['params'] = $item['params'];
            }
            if (!empty($item['action']) && in_array($item['action'], ['add', 'edit'])) {
                $validatedMenuItem['action'] = $item['action'];
            }
            $validatedMenuItems[] = $validatedMenuItem;
        }
        
        $this->fastMenu[$dataProperty] = $validatedMenuItems;
    }
    
    /**
     * Добавить новый контроллера в меню. Чтобы зайдя на этот модуль "Модули" отображался как активный пункт меню
     *
     * @param $vendorModuleController
     * @throws \Exception
     */
    public function addCommonModuleController($vendorModuleController)
    {
        if (in_array($vendorModuleController, $this->modulesControllersHasOwnMenuItem)) {
            return;
        }

        if ($this->module->getBackendControllerParams($vendorModuleController)
            && !in_array($vendorModuleController, $this->leftMenu['left_modules']['left_modules_list'])) {
            $this->leftMenu['left_modules']['left_modules_list'][] = $vendorModuleController;
        }
    }

    public function addCounter($menuItemTitle, $counter)
    {
        $this->menuCounters[$menuItemTitle] = $counter;
    }
    
    public function getCounters()
    {
        foreach ($this->leftMenu as $section=>$menu) {
            foreach (array_keys($menu) as $menuItemTitle) {
                if (isset($this->menuCounters[$menuItemTitle])) {
                    if (!isset($this->menuCounters[$section])) {
                        $this->menuCounters[$section] = $this->menuCounters[$menuItemTitle];
                    } else {
                        $this->menuCounters[$section] += $this->menuCounters[$menuItemTitle];
                    }
                }
            }
        }
        return $this->menuCounters;
    }
    
    /**
     * Получить основное меню админ панели с учетом индивидуальной сортировки менеждера и прав доступа вышеупомянутого менеджера
     *
     * @param $manager
     * @return array
     */
    public function getMenu($manager)
    {
        $controllersPermissions = $this->managers->getControllersPermissions();

        foreach ($this->leftMenu as $section => $items) {
            if (!isset($manager->menu[$section])) {
                $manager->menu[$section] = $this->prepareItemsForManagerMenu($items);
            }

            foreach ($items as $title => $controllers) {
                $mainController = reset($controllers);
                $controllerMethod = null;
                
                if (strpos($mainController, '@') !== false) {
                    list($mainController, $controllerMethod) = explode('@', $mainController, 2);
                }
                
                /*if (!isset($manager->menu[$section][$title])) {
                    $manager->menu[$section][$title] = $mainController;
                }*/

                if (!isset($controllersPermissions[$mainController])) {
                    continue;
                }

                if ($this->managers->hasPermission($manager, $mainController)) {
                    $manager->menu[$section][$title] = [
                        'controller' => $mainController,
                        'controllers_block' => $controllers,
                        'method' => $controllerMethod,
                    ];
                    continue;
                }

                unset($this->leftMenu[$section][$title]);
                if (isset($manager->menu[$section][$title])) {
                    unset($manager->menu[$section][$title]);
                }
            }

            if (empty($section)) {
                unset($this->leftMenu[$section]);

                if (isset($manager->menu[$section])) {
                    unset($manager->menu[$section]);
                }
            }
        }

        foreach($manager->menu as $section => $items) {
            if (empty($this->leftMenu[$section])) {
                unset($manager->menu[$section]);
                continue;
            }

            foreach($items as $title => $controllers) {
                if (empty($this->leftMenu[$section][$title])) {
                    unset($manager->menu[$section][$title]);
                }
            }
        }

        return array_filter($manager->menu);
    }

    public function removeMenuItem($section, $title)
    {
        if (isset($this->leftMenu[$section][$title])) {
            unset($this->leftMenu[$section][$title]);
        }
    }

    private function prepareItemsForManagerMenu($section)
    {
        $preparedItems = [];
        foreach($section as $title => $controllers) {
            $preparedItems[$title] = reset($controllers);
        }

        return $preparedItems;
    }

    /**
     * Данный метод позволяет расширять меню админ панели посредством добавления новых пунктов меню в оную
     *
     * @param $section - ленг корневого пункта меню. Если указать существующий, то пункты меню второго уровня добавляться в конец списка внутри существующего пункта меню
     * @param $menuItemsByControllers - ассоциативный массив с ленгами пунктов меню в качестве ключа и соответствующими им контроллерами в качестве значений
     * @param $icon - путь к файлу относительно папки Backend модуля или текст svg картинки
     * @throws \Exception
     *
     * @example $this->extendBackendMenu('first_level_menu_name', [
    'lang_name_menu_item_1' => ['SomeOneAdmin'],
    'lang_name_menu_item_2' => ['SomeTwoAdmin', 'SomeThreeAdmin'],
    ], 'icon');
     */
    public function extendMenu($section, array $menuItemsByControllers, $icon)
    {
        foreach($menuItemsByControllers as $itemName => $controllers) {
            if (is_string($controllers)) {
                $controllers = [$controllers];
            }

            if (!empty($this->leftMenu[$section][$itemName])) {
                throw new \Exception("Menu item by path {$section} -> {$itemName} already in use");
            }

            $this->leftMenu[$section][$itemName] = $controllers;
            $this->modulesControllersHasOwnMenuItem = array_merge($this->modulesControllersHasOwnMenuItem, $controllers);

            if (empty($icon)) {
                continue;
            }

            $iconObject = ['data' => $icon];
            if (is_file($icon)) {
                $iconObject['type'] = 'file';
            } else {
                $iconObject['type'] = 'text';
            }

            $this->additionalSectionIcons[$section] = $iconObject;
        }
    }

    public function getAdditionalSectionItems()
    {
        return $this->additionalSectionIcons;
    }
    
    public function getActiveControllerName($manager, $controller)
    {
        $activeControllerName = null;
        // Если не запросили модуль - используем модуль первый из разрешенных
        if (empty($controller)
            || (!is_file('backend/Controllers/'.$controller.'.php') && !$this->module->getBackendControllerParams($controller))) {
            $menu = $this->getMenu($manager);
            $firstBlock = reset($menu);
            $activeControllerName = key(reset($firstBlock));
        } else {
            foreach ($this->leftMenu as $section => $items) {
                foreach ($items as $title => $controllers) {
                    foreach ($controllers as $c) {
                        if (strpos($c, '@') !== false) {
                            list($c, $controllerMethod) = explode('@', $c, 2);
                        }
                        if ($controller == $c) {
                            $activeControllerName = $title;
                            break 3;
                        }
                    }
                }
            }
        }
        return $activeControllerName;
    }

    public function getPermissionMenu($manager, $btr = null)
    {
        $permissionMenu = [];

        $menu = $this->leftMenu;
        foreach($menu as $blockName => $items) {
            $permissionMenu[$blockName] = $this->groupPermissionByBlockMenu($items);
        }
        
        $permissionMenu['left_system_controllers'] = $this->groupPermissionByBlockMenu($this->systemControllers);
        
        if (is_null($btr)) {
            return $permissionMenu;
        }

        $permissionMenu = $this->replaceTranslations($btr, $permissionMenu);
        
        // Разрешения для модулей добавляем без переводов, в качестве имени идёт Vendor/Module
        foreach ($this->managers->getModulesPermissions() as $permission=>$vendorModuleName) {
            $permissionMenu['left_modules'][$permission] = $vendorModuleName;
        }

        return $this->removeNotPermittedSections($permissionMenu, $manager);
    }

    private function removeNotPermittedSections($permissionMenu, $manager)
    {
        foreach($permissionMenu as $menuName => $menuItem) {
            foreach($menuItem as $permission => $title) {
                if (! in_array($permission, $manager->permissions)) {
                    unset($permissionMenu[$menuName][$permission]);
                }
            }
        }

        foreach($permissionMenu as $menuName => $menuItem) {
            if (empty($permissionMenu[$menuName])) {
                unset($permissionMenu[$menuName]);
            }
        }

        return $permissionMenu;
    }

    private function replaceTranslations($btr, $permissionMenu)
    {
        foreach($permissionMenu as $blockName => $blockItems) {
            foreach($blockItems as $permission => $title) {
                $permissionMenu[$blockName][$permission] = $btr->$title;
            }
        }

        return $permissionMenu;
    }

    private function groupPermissionByBlockMenu($blockMenu)
    {
        $permissionBlockMenu = [];

        foreach($blockMenu as $itemName => $controllers) {
            if (strpos($controllers[0], '@') !== false) {
                list($controllers[0], $controllerMethod) = explode('@', $controllers[0], 2);
            }
            $permission = $this->managers->getPermissionByController($controllers[0]);
            // Берем первый попавшийся пункт как основной для одинаковых пермишинов, иначе array_flip будет брать последний
            if (!in_array($permission, $permissionBlockMenu)) {
                $permissionBlockMenu[$itemName] = $permission;
            }
        }

        return array_flip($permissionBlockMenu);
    }
}
