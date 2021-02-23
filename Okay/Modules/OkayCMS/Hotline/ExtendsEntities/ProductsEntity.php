<?php


namespace Okay\Modules\OkayCMS\Hotline\ExtendsEntities;


use Okay\Core\Modules\AbstractModuleEntityFilter;
use Okay\Core\QueryFactory;
use Okay\Core\ServiceLocator;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineFeedsEntity;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineRelationsEntity;
use Okay\Modules\OkayCMS\Hotline\Init\Init;

class ProductsEntity extends AbstractModuleEntityFilter
{
    public function okaycms__hotline__feeds($status, $filter)
    {
        if ($status) {
            /** @var ServiceLocator $SL */
            $SL = ServiceLocator::getInstance();

            /** @var QueryFactory $queryFactory */
            $queryFactory = $SL->getService(QueryFactory::class);

            $select = $queryFactory->newSelect();
            $select ->from(HotlineFeedsEntity::getTable())
                ->cols(['*']);
            $feeds = $select->results();

            $cols = [];
            $i = 1;
            foreach ($feeds as $feed) {
                $tableName = Init::TO_FEED_FIELD . '_' . $i;

                $cols[] =
                    "CASE
                    WHEN {$tableName}.feed_id IS NULL
                        THEN 0
                    ELSE 1
                END AS " . $tableName;

                $subSelect = $queryFactory->newSelect();
                $subSelect  ->from(HotlineRelationsEntity::getTable())
                    ->cols([
                        'feed_id',
                        'entity_id'
                    ])
                    ->where("feed_id = :feed_id_{$i} AND entity_type = 'product' AND include = 1");

                $this->select->joinSubSelect(
                    'LEFT',
                    $subSelect->getStatement(),
                    $tableName,
                    "{$tableName}.entity_id = p.id"
                );

                $this->select->bindValue("feed_id_{$i}", $feed->id);
                $i++;
            }
            $this->select->cols($cols);
        }
    }
}