<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

/**
 * класс работает со значениями синонимов значений свойств
 * Класс \Okay\Entities\FeaturesAliases работает с алиасами, он задает "Дательный падеж", "множественное число"...
 * Класс FeaturesValuesAliasesValues работает со значениями синонимов для значений свойств "красному", "красные"...
 */
class FeaturesValuesAliasesValuesEntity extends Entity
{
    protected static $fields = [
        'feature_alias_id',
        'translit',
        'value',
        'lang_id',
        'feature_id'
    ];

    protected static $additionalFields = [
        'fa.variable'
    ];

    protected static $table = '__features_values_aliases_values';
    protected static $tableAlias = 'ov';

    public function find(array $filter = [])
    {
        $this->select->where('lang_id=' . $this->lang->getLangId());
        $this->select->join('left', '__features_aliases AS fa', 'fa.id=ov.feature_alias_id');
        return parent::find($filter);
    }

    public function delete($ids)
    {
        throw new \Exception('Method delete() locked for current Entity');
    }

    public function update($ids, $object)
    {
        throw new \Exception('Method update() locked for current Entity');
    }

    public function count(array $filter = [])
    {
        throw new \Exception('Method count() locked for current Entity');
    }

    public function get($id)
    {
        throw new \Exception('Method get() locked for current Entity');
    }

}