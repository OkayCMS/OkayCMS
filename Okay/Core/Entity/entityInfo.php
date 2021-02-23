<?php


namespace Okay\Core\Entity;


trait entityInfo
{

    /**
     * Метод возвращает все поля сущности, за исключением переданных
     * 
     * @var array $excludedFields поля, которые нужно исключить
     * @return array
     */
    final public static function getDifferentFields($excludedFields)
    {
        $allFields = static::getAllDefaultFields();
        foreach ($excludedFields as $field) {
            if (($fieldKey = array_search($field, $allFields)) !== false && isset($allFields[$fieldKey])) {
                unset($allFields[$fieldKey]);
            }
        }
        
        return (array)$allFields;
    }

    /**
     * Метод возвращает все зарегистрированные поля сущности
     * 
     * @return array
     */
    final public static function getAllDefaultFields()
    {
        $fields = static::getFields();
        $langFields = static::getLangFields();
        $additionalFields = static::getAdditionalFields();
        
        return $allFields = array_merge($fields, $langFields, $additionalFields);
    }
    
    /**
     * @var array $fields
     */
    final public function setSelectFields(array $fields)
    {
        $this->selectFields = array_merge($this->selectFields, $fields);
    }
    
    /**
     * @return array
     */
    final public static function getFields()
    {
        return (array)static::$fields;
    }
    
    /**
     * @return array
     */
    final public static function getAdditionalFields()
    {
        return (array)static::$additionalFields;
    }

    /**
     * @return array
     */
    final public static function getSearchFields()
    {
        return (array)static::$searchFields;
    }

    /**
     * @return array
     */
    final public static function getDefaultOrderFields()
    {
        return (array)static::$defaultOrderFields;
    }

    /**
     * @return string
     */
    final public static function getLangObject()
    {
        return (string)static::$langObject;
    }

    /**
     * @return array
     */
    final public static function getLangFields()
    {
        return (array)static::$langFields;
    }

    /**
     * @return string|null
     */
    final public static function getLangTable()
    {
        $table = (string)static::$langTable;
        if (empty($table)) {
            return null;
        }
        return '__lang_' . preg_replace('~(__lang_)?(.+)~', '$2', $table);
    }

    /**
     * @return string
     */
    final public static function getTable()
    {
        $table = (string)static::$table;
        return '__' . preg_replace('~(__)?(.+)~', '$2', $table);
    }

    /**
     * @return string
     */
    final public static function getTableAlias()
    {
        if (empty(static::$tableAlias)) {
            static::$tableAlias = substr(preg_replace('~(__)?(.+)~', '$2', self::getTable()), 0, 1);
        }
        return (string)static::$tableAlias;
    }

    /**
     * @return string
     */
    final public static function getAlternativeIdField()
    {
        return (string)static::$alternativeIdField;
    }

    final public static function setLangTable($langTable)
    {
        static::$langTable = $langTable;
    }

    final public static function setLangObject($langObject)
    {
        static::$langObject = $langObject;
    }

    final public static function addField($name)
    {
        if (!in_array($name, static::getFields())) {
            // Если это поле отмечено как ленговое, но его регистрируют как не ленговое, удалим его из ленговых
            if (in_array($name, static::getLangFields())) {
                $langFields = static::getLangFields();
                unset($langFields[array_search($name, $langFields)]);
                static::$langFields = $langFields;
            }
            static::$fields[] = $name;
        }
    }

    final public static function addAdditionalField($name)
    {
        if (!in_array($name, static::getAdditionalFields())) {
            static::$additionalFields[] = $name;
        }
    }

    final public static function addLangField($name)
    {
        if (!in_array($name, static::getLangFields())) {
            // Если это поле отмечено как не ленговое, но его регистрируют как ленговое, удалим его из не ленговых
            if (in_array($name, static::getFields())) {
                $fields = static::getFields();
                unset($fields[array_search($name, $fields)]);
                static::$fields = $fields;
            }
            static::$langFields[] = $name;
        }
    }
}