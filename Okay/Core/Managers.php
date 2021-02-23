<?php


namespace Okay\Core;


class Managers
{
    
    private $modulesPermissionsList = [];
    private $modulesControllersPermissions = [];
    
    /*Список параметров доступа для менеджера сайта*/
    private $permissionsList = [
        'products',
        'categories',
        'brands',
        'features',
        'orders',
        'order_settings',
        'discounts_settings',
        'users',
        'groups',
        'coupons',
        'pages',
        'blog',
        'blog_categories',
        'authors',
        'comments',
        'feedbacks',
        'import',
        'export',
        'settings',
        'currency',
        'delivery',
        'payment',
        'managers',
        'license',
        'languages',
        'callbacks',
        'robots',
        'seo_patterns',
        'support',
        'subscribes',
        'menu',
        'seo_filter_patterns',
        'settings_counter',
        'features_aliases',
        'file_templates',
        'style_templates',
        'scripts',
        'images',
        'translations',
        'design_settings',
        'sales_chart',
        'sales_report',
        'category_stats',
        'modules',
        'theme',
        'learning',
        'router',
        'system_modules',
        'settings_indexing',
    ];

    // Соответствие модулей и названий соответствующих прав
    private $controllersPermissions = [
        'ProductsAdmin'       => 'products',
        'ProductAdmin'        => 'products',
        'CategoriesAdmin'     => 'categories',
        'CategoryAdmin'       => 'categories',
        'BrandsAdmin'         => 'brands',
        'BrandAdmin'          => 'brands',
        'FeaturesAdmin'       => 'features',
        'FeatureAdmin'        => 'features',
        'OrdersAdmin'         => 'orders',
        'OrderAdmin'          => 'orders',
        'UsersAdmin'          => 'users',
        'UserAdmin'           => 'users',
        'ExportUsersAdmin'    => 'users',
        'UserGroupsAdmin'     => 'groups',
        'UserGroupAdmin'      => 'groups',
        'CouponsAdmin'        => 'coupons',
        'PagesAdmin'          => 'pages',
        'PageAdmin'           => 'pages',
        'MenusAdmin'          => 'menu',
        'MenuAdmin'           => 'menu',
        'BlogAdmin'           => 'blog',
        'PostAdmin'           => 'blog',
        'BlogCategoriesAdmin' => 'blog_categories',
        'BlogCategoryAdmin'   => 'blog_categories',
        'AuthorsAdmin'        => 'authors',
        'AuthorAdmin'         => 'authors',
        'CommentsAdmin'       => 'comments',
        'FeedbacksAdmin'      => 'feedbacks',
        'ImportAdmin'         => 'import',
        'ImportLogAdmin'      => 'import',
        'ExportAdmin'         => 'export',
        'StatsAdmin'          => 'sales_chart',
        'ThemeAdmin'          => 'theme',
        'StylesAdmin'         => 'style_templates',
        'TemplatesAdmin'      => 'file_templates',
        'EmailTemplatesAdmin' => 'file_templates',
        'ImagesAdmin'         => 'images',
        'ScriptsAdmin'        => 'scripts',
        'SettingsThemeAdmin'  => 'design_settings',
        'SettingsGeneralAdmin'  => 'settings',
        'SettingsNotifyAdmin'   => 'settings',
        'SettingsCatalogAdmin'  => 'settings',
        'SettingsCounterAdmin'  => 'settings_counter',
        'SettingsFeedAdmin'     => 'settings',
        'SystemAdmin'           => 'settings',
        'CurrencyAdmin'         => 'currency',
        'DeliveriesAdmin'       => 'delivery',
        'DeliveryAdmin'         => 'delivery',
        'PaymentMethodAdmin'    => 'payment',
        'PaymentMethodsAdmin'   => 'payment',
        'ManagersAdmin'         => 'managers',
        'ManagerAdmin'          => 'managers',
        'LicenseAdmin'          => 'license',
        'SubscribeMailingAdmin' => 'subscribes',
        'CallbacksAdmin'        => 'callbacks',
        'LanguageAdmin'         => 'languages',
        'LanguagesAdmin'        => 'languages',
        'TranslationAdmin'      => 'translations',
        'TranslationsAdmin'     => 'translations',
        'ReportStatsAdmin'      => 'sales_report',
        'CategoryStatsAdmin'    => 'category_stats',
        'RobotsAdmin'               => 'robots',
        'OrderSettingsAdmin'        => 'order_settings',
        'DiscountsSettingsAdmin'    => 'discounts_settings',
        'SeoPatternsAdmin'          => 'seo_patterns',
        'SeoFilterPatternsAdmin'    => 'seo_filter_patterns',
        'SupportAdmin'              => 'support',
        'TopicAdmin'                => 'support',
        'FeaturesAliasesAdmin'      => 'features_aliases',
        'ModulesAdmin'              => 'modules',
        'ModuleDesignAdmin'         => 'modules',
        'LearningAdmin'             => 'learning',
        'SettingsRouterAdmin'       => 'router',
        'SettingsIndexingAdmin'     => 'settings_indexing',
    ];
    
    /**
     * Добавление разрешения для модуля.
     * @param $permission
     * @param $vendorModuleName
     * @throws \Exception
     */
    public function addModulePermission($permission, $vendorModuleName)
    {
        if (!isset($this->modulesPermissionsList[$permission])) {
            $this->modulesPermissionsList[$permission] = $vendorModuleName;
        }
    }

    public function removeControllersPermissionByModuleName($moduleName)
    {
        unset($this->controllersPermissions[$moduleName]);
    }

    public function hasPermission($manager, $controller)
    {
        $controllersPermissions = $this->getControllersPermissions();
        return in_array($controllersPermissions[$controller], $manager->permissions);
    }

    // Метод возвращает все разрешения, которые были добавлены модулями
    public function getModulesPermissions()
    {
        return $this->modulesPermissionsList;
    }
    
    /**
     * Добавление связки разрешения и контроллера админки.
     * Используется модулями, которые регистрируют контроллеры для админки
     * @param $vendorModuleController
     * @param $permission
     * @throws \Exception
     */
    public function addModuleControllerPermission($vendorModuleController, $permission)
    {
        
        if (isset($this->modulesControllersPermissions[$vendorModuleController])) {
            throw new \Exception("Permission for controller \"{$vendorModuleController}\" already exists");
        }
        
        $this->modulesControllersPermissions[$vendorModuleController] = $permission;
    }
    
    public function setManagerPermissions($manager)
    {
        if (empty($manager)) {
            return false;
        }

        if (is_null($manager->permissions)) {
            $permissions = array_merge($this->permissionsList, array_keys($this->modulesPermissionsList));
            $manager->permissions = $permissions;
            return false;
        }

        $manager->permissions = explode(',', $manager->permissions);
        foreach ($manager->permissions as &$permission) {
            $permission = trim($permission);
        }

        return true;
    }
    
    public function getAllPermissions()
    {
        return array_merge($this->permissionsList, array_keys($this->modulesPermissionsList));
    }

    public function getControllersPermissions()
    {
        $controllersPermissions = $this->controllersPermissions;
        foreach ($this->modulesControllersPermissions as $controller => $modulePermission)  {
            $controllersPermissions[$controller] = $modulePermission;
        }

        return $controllersPermissions;
    }

    public function access($permission, $manager)
    {
        if (empty($permission)) {
            return false;
        }

        if (is_array($manager->permissions)) {
            return in_array($permission, $manager->permissions);
        }

        return false;
    }

    public function getPermissionByController($controller)
    {
        $controllersPermissions = $this->getControllersPermissions();
        
        if (!isset($controllersPermissions[$controller])) {
            return false;
        }
        return $controllersPermissions[$controller];
    }

    public function determineNewPermissions($activeManager, $targetManager, $updatePermissions)
    {
        if ($targetManager) {
            $permissions = $targetManager->permissions;
        } else {
            $permissions = [];
        }

        $allowToUpdatePermissions = $activeManager->permissions;
        foreach($allowToUpdatePermissions as $permission) {
            if (in_array($permission, $updatePermissions) && !in_array($permission, $permissions)) {
                $permissions[] = $permission;
            }
            elseif(!in_array($permission, $updatePermissions) && in_array($permission, $permissions)) {
                $targetKey = array_search($permission, $permissions);
                unset($permissions[$targetKey]);
            }
        }

        return $permissions;
    }

    /*Проверка пароля*/
    public function checkPassword($password, $crypt_pass)
    {
        $salt = explode('$', $crypt_pass);
        $salt = $salt[2];
        return ($crypt_pass == $this->cryptApr1Md5($password, $salt));
    }

    /*Шифрование пароля*/
    public function cryptApr1Md5($plainpasswd, $salt = '')
    {
        if (empty($salt)) {
            $salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
        }
        $len = strlen($plainpasswd);
        $text = $plainpasswd.'$apr1$'.$salt;
        $bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
        for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
        for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $plainpasswd[0]; }
        $bin = pack("H32", md5($text));
        for($i = 0; $i < 1000; $i++) {
            $new = ($i & 1) ? $plainpasswd : $bin;
            if ($i % 3) $new .= $salt;
            if ($i % 7) $new .= $plainpasswd;
            $new .= ($i & 1) ? $bin : $plainpasswd;
            $bin = pack("H32", md5($new));
        }
        $tmp = '';
        for ($i = 0; $i < 5; $i++) {
            $k = $i + 6;
            $j = $i + 12;
            if ($j == 16) $j = 5;
            $tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
        }
        $tmp = chr(0).chr(0).$bin[11].$tmp;
        $tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
            "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
        return "$"."apr1"."$".$salt."$".$tmp;
    }

    public function canVisibleSystemModules($manager)
    {
        return in_array('system_modules', $manager->permissions);
    }

    public function cannotVisibleSystemModules($manager)
    {
        return !$this->canVisibleSystemModules($manager);
    }
}
