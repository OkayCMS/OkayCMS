<?php

namespace Okay\Core\Entity;

trait entityInfo
{
    /**
     * Метод возвращает все поля сущности, за исключением переданных
     *
     * @param array<int|string, string> $excludedFields поля, которые нужно исключить
     * @return list<string>
     */
    final public static function getDifferentFields($excludedFields)
    {
        $allFields = static::getAllDefaultFields();
        foreach ($excludedFields as $field) {
            $fieldKey = array_search($field, $allFields);
            if ($fieldKey !== false) {
                unset($allFields[$fieldKey]);
            }
        }

        return array_values($allFields);
    }

    /**
     * Метод возвращает все зарегистрированные поля сущности
     *
     * @return list<string>
     */
    final public static function getAllDefaultFields()
    {
        $fields = static::getFields();
        $langFields = static::getLangFields();
        $additionalFields = static::getAdditionalFields();

        return array_merge($fields, $langFields, $additionalFields);
    }

    /**
     * @param array<int|string, string> $fields
     */
    final public function setSelectFields(array $fields)
    {
        $this->selectFields = array_values(array_merge($this->selectFields, $fields));
    }

    /**
     * @return list<string>
     */
    final public static function getFields()
    {
        return (array)static::$fields;
    }

    /**
     * @return list<string>
     */
    final public static function getAdditionalFields()
    {
        return (array)static::$additionalFields;
    }

    /**
     * @return list<string>
     */
    final public static function getSearchFields()
    {
        return (array)static::$searchFields;
    }

    /**
     * @return list<string>
     */
    final public static function getDefaultOrderFields()
    {
        return (array)static::$defaultOrderFields;
    }

    /**
     * Метод добавляет новые столбцы для сортировки по умолчанию или переопределяет полностью переменную сортировки по умолчанию у сущности
     *
     * @param list<string> $newOrderFields
     * @param bool $redefine
     * @return list<string>
     */
    final public static function setDefaultOrderFields($newOrderFields, $redefine = false)
    {
        if (!empty($redefine) && !empty($newOrderFields)) {
            return static::$defaultOrderFields = $newOrderFields;
        }

        return static::$defaultOrderFields = array_merge($newOrderFields, static::$defaultOrderFields);
    }

    /**
     * @return string
     */
    final public static function getLangObject()
    {
        return (string)static::$langObject;
    }

    /**
     * @return list<string>
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
            $table = preg_replace('~(__)?(.+)~', '$2', self::getTable());
            static::$tableAlias = substr(is_string($table) ? $table : self::getTable(), 0, 1);
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
                static::$langFields = array_values($langFields);
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
                static::$fields = array_values($fields);
            }
            static::$langFields[] = $name;
        }
    }
}
