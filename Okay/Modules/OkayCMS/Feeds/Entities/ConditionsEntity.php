<?php

namespace Okay\Modules\OkayCMS\Feeds\Entities;

use Okay\Core\Entity\Entity;
use Okay\Modules\OkayCMS\Feeds\Init\Init;

class ConditionsEntity extends Entity
{
    static protected $fields = [
        'id',
        'feed_id',
        'entity',
        'type',
        'all_entities'
    ];

    protected static $table = 'okay_cms__feeds__conditions';
    protected static $tableAlias = 'oc_fc';

    public function find(array $filter = []): array
    {
        $this->select->cols([
            '(SELECT GROUP_CONCAT(entity_id) FROM '.Init::CONDITIONS_ENTITIES_RELATION_TABLE.' WHERE condition_id = id) AS "entity_ids"'
        ]);

        $conditions = parent::find($filter);

        foreach ($conditions as $condition) {
            $condition->entity_ids = $condition->entity_ids === null ? [] : explode(',', $condition->entity_ids);
        }

        return $conditions;
    }

    public function delete($ids): bool
    {
        if ($result = parent::delete($ids)) {
            $this->queryFactory->newDelete()
                ->from(Init::CONDITIONS_ENTITIES_RELATION_TABLE)
                ->where('condition_id IN (:condition_ids)')
                ->bindValues(['condition_ids' => $ids])
                ->execute();
        }

        return $result;
    }

    public function duplicate($conditionId, $newFeedId)
    {
        $condition = $this->findOne(['id' => $conditionId]);

        $newCondition = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($condition, $field)) {
                $newCondition->$field = $condition->$field;
            }
        }

        $newCondition->id = null;
        $newCondition->feed_id = $newFeedId;

        $newConditionId = $this->add($newCondition);

        $this->duplicateConditionsEntities($condition->id, $newConditionId);

        return $newConditionId;
    }

    private function duplicateConditionsEntities($conditionId, $newConditionId): void
    {
        $this->queryFactory->newSqlQuery()
            ->setStatement('INSERT INTO '.Init::CONDITIONS_ENTITIES_RELATION_TABLE.' SELECT :new_condition_id, `entity_id` FROM '.Init::CONDITIONS_ENTITIES_RELATION_TABLE.' WHERE condition_id = :condition_id;')
            ->bindValues([
                'condition_id' => $conditionId,
                'new_condition_id' => $newConditionId
            ])
            ->execute();
    }

    public function deleteByDiscountId($feedIds): void
    {
        $feedIds = (array) $feedIds;

        $ids = $this->queryFactory->newSelect()
            ->from(ConditionsEntity::getTable())
            ->cols(['id'])
            ->where('feed_id IN (:feed_ids)')
            ->bindValues(['feed_ids' => $feedIds])
            ->results('id');

        $this->delete($ids);
    }

    public function updateConditionEntities($conditionId, array $entityIds): void
    {
        $this->queryFactory->newDelete()
            ->from(Init::CONDITIONS_ENTITIES_RELATION_TABLE)
            ->where('condition_id = :condition_id')
            ->bindValues(['condition_id' => $conditionId])
            ->execute();

        if (!empty($entityIds)) {
            $entityIds = array_unique($entityIds);
            $insert = $this->queryFactory->newInsert()
                ->into(Init::CONDITIONS_ENTITIES_RELATION_TABLE);
            foreach ($entityIds as $entityId) {
                $insert->addRow([
                    'condition_id' => $conditionId,
                    'entity_id' => $entityId
                ]);
            }
            $insert->getStatement();
            $insert->execute();
        }
    }
}