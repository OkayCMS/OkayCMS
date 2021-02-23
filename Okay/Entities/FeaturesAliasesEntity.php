<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Translit;

class FeaturesAliasesEntity extends Entity
{
    protected static $fields = [
        'id',
        'variable',
        'position',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $searchFields = [];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = '__features_aliases';
    protected static $langObject = 'feature_alias';
    protected static $langTable = 'features_aliases';
    protected static $tableAlias = 'fa';
    protected static $alternativeIdField = 'variable';

    public function add($alias)
    {
        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);
        
        $alias = (array) $alias;
        if (empty($alias['variable'])) {
            $alias['variable'] = $translit->translit($alias['name']);
            $alias['variable'] = strtolower(preg_replace("/[^0-9a-z]+/ui", '', $alias['variable']));
        }

        // Если есть склонение с такой переменной, добавляем к ней число
        while ($this->get((string)$alias['variable'])) {
            if (preg_match('/(.+)_([0-9]+)$/', $alias['variable'], $parts)) {
                $alias['variable'] = $parts[1].'_'.($parts[2]+1);
            } else {
                $alias['variable'] = $alias['variable'].'_2';
            }
        }
        return parent::add($alias);
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (empty($ids)) {
            return false;
        }

        $delete = $this->queryFactory->newDelete();
        $delete->from(FeaturesValuesAliasesValuesEntity::getTable())
            ->where('feature_alias_id IN (:feature_alias_ids)')
            ->bindValue('feature_alias_ids', $ids);
        $this->db->query($delete);

        return parent::delete($ids);
    }
    
}