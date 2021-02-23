<?php


namespace Okay\Core\Modules;


class UpdateObject
{
    private static $objects = [];

    public function register($alias, $permission, $entityClassName)
    {
        if (in_array($alias, array_keys(self::$objects))) {
            throw new \Exception("Alias \"{$alias}\" already exists");
        }

        $object = new \stdClass();
        $object->permission = $permission;
        $object->entityName = $entityClassName;

        self::$objects[$alias] = $object;
    }

    public function getObjects()
    {
        return self::$objects;
    }

    public function getByAlias($alias)
    {
        if (!in_array($alias, array_keys(self::$objects))) {
            return false;
        }

        return self::$objects[$alias];
    }
}