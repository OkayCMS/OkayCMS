<?php


namespace Okay\Modules\OkayCMS\Hotline\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class HotlineRelationsEntity extends Entity
{
    protected static $fields = [
        'id',
        'feed_id',
        'entity_id',
        'entity_type',
        'include'
    ];

    protected static $table = 'okaycms__hotline__relations';

    protected static $tableAlias = 'hxr';

    /**
     * Удаляем все категории
     */
    public function removeAllCategories()
    {
        $delete = $this->queryFactory->newDelete();
        $delete ->from($this->getTable())
                ->where("entity_type = 'category'")
                ->execute();

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());

    }

    /**
     * @param string|integer $feedId
     * Удаляем все категории закреплённые за определенным фидом
     */
    public function removeAllCategoriesByFeedId($feedId)
    {
        $delete = $this->queryFactory->newDelete();
        $delete ->from($this->getTable())
                ->where("entity_type = 'category'")
                ->where('feed_id = :feed_id')
                ->bindValue('feed_id', $feedId)
                ->execute();

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /**
     * Удаляем все бренды
     */
    public function removeAllBrands()
    {
        $delete = $this->queryFactory->newDelete();
        $delete ->from($this->getTable())
                ->where("entity_type = 'brand'")
                ->execute();

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /**
     * @param string|integer $feedId
     * Удаляем все бренды закреплённые за определенным фидом
     */
    public function removeAllBrandsByFeedId($feedId)
    {
        $delete = $this->queryFactory->newDelete();
        $delete ->from($this->getTable())
                ->where("entity_type = 'brand'")
                ->where('feed_id = :feed_id')
                ->bindValue('feed_id', $feedId)
                ->execute();

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /**
     * Удаляем все продукты
     */
    public function removeAllRelatedProducts()
    {
        $delete = $this->queryFactory->newDelete();
        $delete ->from($this->getTable())
                ->where("entity_type = 'product' AND include = 1")
                ->execute();

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /**
     * Удаляем все закрепленные продукты не для выгрузки
     */
    public function removeAllNotRelatedProducts()
    {
        $delete = $this->queryFactory->newDelete();
        $delete ->from($this->getTable())
                ->where("entity_type = 'product' AND include = 0")
                ->execute();

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /**
     * @param array $rows
     * Добавляем отношения
     */
    public function addRelations($rows)
    {
        if (!empty($rows)) {
            $insert = $this->queryFactory->newInsert();
            $insert ->into($this->getTable())
                    ->addRows($rows);
            $insert ->getStatement(); //Todo баг либы, только 1 или больше 2 записей
            $insert ->execute();
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }
}