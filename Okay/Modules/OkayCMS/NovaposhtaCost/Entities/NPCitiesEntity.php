<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Entities;


use Okay\Core\Entity\Entity;

class NPCitiesEntity extends Entity
{
    protected static $fields = [
        'id',
        'ref',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $table = 'okaycms__np_cities';
    protected static $langTable = 'okaycms__np_cities';
    protected static $langObject = 'city';
    protected static $tableAlias = 'npc';
    
    protected static $defaultOrderFields = [
        'name'
    ];
    
    protected static $searchFields = [
        'name'
    ];
    
    public function filter__keyword($keywords)
    {
        $keywords = explode(' ', $keywords);

        $tableAlias = $this->getTableAlias();
        $langAlias = $this->lang->getLangAlias(
            $this->getTableAlias()
        );

        $fields = $this->getFields();
        $langFields = $this->getLangFields();

        $searchFields = $this->getSearchFields();
        foreach ($keywords as $keyNum=>$keyword) {
            $keywordFilter = [];
            foreach ($searchFields as $searchField) {
                $searchFieldWithAlias = $searchField;

                if (in_array($searchField, $fields)) {
                    $searchFieldWithAlias = $tableAlias . "." . $searchField;
                } elseif (in_array($searchField, $langFields)) {
                    $searchFieldWithAlias = $langAlias . "." . $searchField;
                }

                $keywordFilter[] = $searchFieldWithAlias . " LIKE :auto_keyword_{$searchField}_{$keyNum}";
                $this->select->bindValue("auto_keyword_{$searchField}_{$keyNum}", $keyword . '%');
            }
            $this->select->where('(' . implode(' OR ', $keywordFilter) . ')');

        }
    }

}