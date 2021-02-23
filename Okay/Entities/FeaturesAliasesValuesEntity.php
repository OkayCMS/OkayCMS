<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

/**
 * класс работает со значениями синонимов свойств
 * Класс \Okay\Entities\FeaturesAliases работает с алиасами, он задает "Дательный падеж", "множественное число"...
 * Класс FeaturesAliasesValues работает со значениями синонимов для свойств "цвету", "цвета"...
 */
class FeaturesAliasesValuesEntity extends Entity
{
    protected static $fields = [
        'id',
        'feature_alias_id',
        'feature_id'
    ];

    protected static $langFields = [
        'value',
    ];
    
    protected static $additionalFields = [
        'fa.variable',
    ];

    protected static $table = '__features_aliases_values';
    protected static $langObject = 'feature_alias_value';
    protected static $langTable = 'features_aliases_values';
    protected static $tableAlias = 'f';

    public function find(array $filter = [])
    {
        $this->select->join('LEFT', '__features_aliases AS fa', 'fa.id=f.feature_alias_id');
        return parent::find($filter);
    }

}