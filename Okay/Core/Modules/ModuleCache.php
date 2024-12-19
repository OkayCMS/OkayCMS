<?php

namespace Okay\Core\Modules;

class ModuleCache
{
    private const __EXPIRE = 2;
    protected static $timeExpire = 0;

    /**
     * @var object[] array of ModulesEntity objects
     */
    protected static $modules = [];

    public static function set($modules)
    {
        self::$timeExpire = time() + self::__EXPIRE;
        self::$modules = $modules ?? [];
    }

    public static function get($vendor, $moduleName)
    {
        $module = null;
        if (!empty(self::$modules)) {
            if (self::$timeExpire > time()) {
                foreach (self::$modules as $moduleItem) {
                    if ($moduleItem->vendor == $vendor && $moduleItem->module_name == $moduleName) {
                        $module = $moduleItem;
                    }
                }
            } else {
                self::flush();
            }
        }

        return $module;
    }

    public static function flush()
    {
        self::$timeExpire = 0;
        self::set([]);
    }
}