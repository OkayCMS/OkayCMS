<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Entities;


use Okay\Core\Entity\Entity;

class NPCitiesEntity extends Entity
{
    protected static $fields = [
        'id',
        'ref',
        'updated_at',
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

    public function add($object)
    {
        $object = (object)$object;
        $object->updated_at = 'NOW()';
        return parent::add($object);
    }

    public function update($ids, $object)
    {
        $object = (object)$object;
        $object->updated_at = 'NOW()';
        parent::update($ids, $object);
    }

    public function removeRedundant(string $updatedAt)
    {
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement(sprintf('
                DELETE npc, l FROM %s npc
                INNER JOIN %s l ON l.city_id = npc.id
                WHERE
                npc.updated_at < :updated_at
            ',
            self::getTable(),
            self::getLangTable()
        ))->bindValues([
            'updated_at' => $updatedAt,
        ]);
        $this->db->query($sql);
    }

    public function filter__keyword($keywords)
    {
        $keywords = (array)$keywords;

        $subQuery = $this->queryFactory->newSelect();
        $subQuery->from(self::getLangTable())
        ->cols([
            'city_id'
        ])->groupBy([
            'city_id'
        ]);

        $searchFields = $this->getSearchFields();
        foreach ($keywords as $keyNum=>$keyword) {
            $keywordFilter = [];
            foreach ($searchFields as $searchField) {
                $keywordFilter[] = "{$searchField} LIKE :multi_lang_keyword_{$searchField}_{$keyNum}";
                $this->select->bindValue("multi_lang_keyword_{$searchField}_{$keyNum}", $keyword . '%');
            }
            $subQuery->where('(' . implode(' OR ', $keywordFilter) . ')');
        }

        $this->select->joinSubSelect(
            'INNER',
            $subQuery->getStatement(),
            'multi_lang_keyword',
            sprintf(
                'multi_lang_keyword.city_id=%s.id',
                self::getTableAlias()
            )
        );
    }
}