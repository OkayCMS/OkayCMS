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

    public function find(array $filter = [])
    {
        $result = parent::find($filter);
        if(empty($result)){
            $currentLanguageId = $_SESSION['lang_id'];
            $languages = $this->lang->getAllLanguages();
            foreach($languages as $lang){
                if($lang->id == $currentLanguageId) continue;

                $this->lang->setLangId($lang->id);
                $filter['limit'] = (!empty($filter['limit'])) ? $filter['limit'] : 20;
                $langResult = parent::find($filter);
                if(!empty($langResult)){
                    $refs = [];
                    foreach($langResult as $row){
                        if(!empty($row->ref))
                            $refs[] = $row->ref;
                    }
                    $this->lang->setLangId($currentLanguageId);
                    $result = parent::find(['ref' => $refs]);
                    break;
                }
            }
        }
        return $result;
    }

    public function filter__keyword($keywords)
    {
        $keywords = (array)$keywords;

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