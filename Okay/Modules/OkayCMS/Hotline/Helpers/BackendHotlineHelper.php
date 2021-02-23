<?php


namespace Okay\Modules\OkayCMS\Hotline\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\ProductsHelper;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineFeedsEntity;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineRelationsEntity;

class BackendHotlineHelper
{
    /** @var QueryFactory */
    private $queryFactory;

    /** @var Request */
    private $request;

    /** @var ProductsHelper */
    private $productsHelper;


    /** @var HotlineFeedsEntity */
    private $feedsEntity;

    /** @var HotlineRelationsEntity */
    private $relationsEntity;

    public function __construct(
        EntityFactory  $entityFactory,
        QueryFactory   $queryFactory,
        Request        $request,
        ProductsHelper $productsHelper
    )
    {
        $this->queryFactory   = $queryFactory;
        $this->request        = $request;
        $this->productsHelper = $productsHelper;

        $this->feedsEntity     = $entityFactory->get(HotlineFeedsEntity::class);
        $this->relationsEntity = $entityFactory->get(HotlineRelationsEntity::class);
    }

    /**
     * @param array $feed
     * Добавляем новый фид
     * @return integer|bool
     */
    public function addFeed($feed = [
        'name' => 'New Feed',
        'url' => '',
        'enabled' => 0
    ])
    {
        if (empty($feed['url'])) {
            $feed['url'] = $this->feedsEntity->count() + 1;

            while ($this->feedsEntity->findOne(['url' => $feed['url']])) {
                $feed['url']++;
            }
        }

        $feedId = $this->feedsEntity->add($feed);

        return ExtenderFacade::execute(__METHOD__, $feedId, func_get_args());
    }

    /**
     * @param string|integer $feedId
     * Удаляем фид
     */
    public function removeFeed($feedId)
    {
        $this->feedsEntity->delete($feedId);
    }

    /**
     * @param array $feeds
     * Обновляем полученные фиды
     */
    public function updateFeeds($feeds)
    {
        foreach ($feeds as $feedId => $feed) {
            $this->feedsEntity->update($feedId, $feed);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param string|integer|array $feeds
     * Валидируем фиды. Проверяем URL на уникальность
     * @return array
     * Возвращаем ошибки, индивидуальные для каждого фида
     */
    public function validateFeeds($feeds)
    {
        $errors = [];
        foreach ($feeds as $feedId => $feed) {
            if (($dbFeed = $this->feedsEntity->findOne(['url' => $feed['url']])) && ($dbFeed->id != $feedId)) {
                $errors['feeds'][$feedId]['url'] = true;
            } else if (preg_match('/[А-я]/', $feed['url'])) {
                $errors['feeds'][$feedId]['url_cyrillic'] = true;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $errors, func_get_args());
    }

    /**
     * @param string|integer $feedId
     * Закрепляяем все категории за фидом
     */
    public function addAllCategories($feedId)
    {
        $this->relationsEntity->removeAllCategoriesByFeedId($feedId);

        $select = $this->queryFactory->newSelect();
        $select ->from(CategoriesEntity::getTable())
            ->cols(['id']);
        $categoriesIds = $select->results('id');
        $rows = [];
        foreach ($categoriesIds as $categoryId) {
            $rows[] = [
                'feed_id'     => $feedId,
                'entity_id'   => $categoryId,
                'entity_type' => 'category',
                'include'     => 1
            ];
        }

        $this->relationsEntity->addRelations($rows);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param array $relatedCategories
     * Закрепляем за фидом вручуню отмеченные категории
     */
    public function updateRelatedCategories($relatedCategories)
    {
        $this->relationsEntity->removeAllCategories();

        if (!empty($relatedCategories)) {
            $rows = [];
            foreach ($relatedCategories as $feedId => $categoriesIds) {
                foreach ($categoriesIds as $categoryId) {
                    $rows[] = [
                        'feed_id'     => $feedId,
                        'entity_id'   => $categoryId,
                        'entity_type' => 'category',
                        'include'     => 1
                    ];
                }
            }

            $this->relationsEntity->addRelations($rows);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param string|integer $feedId
     * Закрепяем все бренды за фидом
     */
    public function addAllBrands($feedId)
    {
        $this->relationsEntity->removeAllBrandsByFeedId($feedId);

        $select = $this->queryFactory->newSelect();
        $select ->from(BrandsEntity::getTable())
            ->cols(['id']);
        $brandsIds = $select->results('id');
        $rows = [];
        foreach ($brandsIds as $brandId) {
            $rows[] = [
                'feed_id'     => $feedId,
                'entity_id'   => $brandId,
                'entity_type' => 'brand',
                'include'     => 1
            ];
        }

        $this->relationsEntity->addRelations($rows);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param array $relatedBrands
     * Закрепляем за фидом вручуню отмеченные бренды
     */
    public function updateRelatedBrands($relatedBrands)
    {
        $this->relationsEntity->removeAllBrands();

        if (!empty($relatedBrands)) {
            $rows = [];
            foreach ($relatedBrands as $feedId => $brandsIds) {
                foreach ($brandsIds as $brandId) {
                    $rows[] = [
                        'feed_id'     => $feedId,
                        'entity_id'   => $brandId,
                        'entity_type' => 'brand',
                        'include'     => 1
                    ];
                }
            }

            $this->relationsEntity->addRelations($rows);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Закрепляем за фидом вручуню отмеченные продукты
     */
    public function updateRelatedProducts()
    {
        $this->relationsEntity->removeAllRelatedProducts();

        $feeds = $this->feedsEntity->find(['limit' => $this->feedsEntity->count()]);

        $rows = [];
        foreach ($feeds as $feed) {
            $relatedProducts = $this->request->post("related_products_{$feed->id}");
            if (!empty($relatedProducts)) {
                $relatedProducts = array_unique($relatedProducts);
                foreach ($relatedProducts as $productId) {
                    $rows[] = [
                        'feed_id'     => $feed->id,
                        'entity_id'   => $productId,
                        'entity_type' => 'product',
                        'include'     => 1
                    ];
                }
            }
        }

        $this->relationsEntity->addRelations($rows);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Закрепляем за фидом вручуню отмеченные продукты не для выгрузки
     */
    public function updateNotRelatedProducts()
    {
        $this->relationsEntity->removeAllNotRelatedProducts();

        $feeds = $this->feedsEntity->find(['limit' => $this->feedsEntity->count()]);

        $rows = [];
        foreach ($feeds as $feed) {
            $notRelatedProducts = $this->request->post("not_related_products_{$feed->id}");
            if (!empty($notRelatedProducts)) {
                $notRelatedProducts = array_unique($notRelatedProducts);
                foreach ($notRelatedProducts as $productId) {
                    $rows[] = [
                        'feed_id'     => $feed->id,
                        'entity_id'   => $productId,
                        'entity_type' => 'product',
                        'include'     => 0
                    ];
                }
            }
        }

        $this->relationsEntity->addRelations($rows);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @return array
     * Достаем массив ids закрепённых категорий
     */
    public function getAllRelatedCategoriesIds()
    {
        $allCategoriesRelations = $this->relationsEntity->find([
            'limit' => $this->relationsEntity->count(),
            'entity_type' => 'category'
        ]);

        $relatedCategoriesIds = [];
        foreach ($allCategoriesRelations as $categoryRelation) {
            $relatedCategoriesIds[$categoryRelation->feed_id][] = $categoryRelation->entity_id;
        }

        return ExtenderFacade::execute(__METHOD__, $relatedCategoriesIds, func_get_args());
    }

    /**
     * @return array
     * Достаем массив ids закрепённых брендов
     */
    public function getAllRelatedBrandsIds()
    {
        $allBrandsRelations = $this->relationsEntity->find([
            'limit' => $this->relationsEntity->count(),
            'entity_type' => 'brand'
        ]);

        $relatedBrandsIds = [];
        foreach ($allBrandsRelations as $brandRelation) {
            $relatedBrandsIds[$brandRelation->feed_id][] = $brandRelation->entity_id;
        }

        return ExtenderFacade::execute(__METHOD__, $relatedBrandsIds, func_get_args());
    }

    /**
     * @return array
     * Достаем массив закрепённых продуктов
     * @throws \Exception
     */
    public function getAllRelatedProducts()
    {
        $allRelatedProductsRelations = $this->relationsEntity->find([
            'limit' => $this->relationsEntity->count(),
            'entity_type' => 'product',
            'include' => 1
        ]);

        $relatedProductsIds = [];
        foreach ($allRelatedProductsRelations as $relation) {
            $relatedProductsIds[] = $relation->entity_id;
        }

        $products = $this->productsHelper->getList(['id' => $relatedProductsIds]);

        $relatedProducts = [];
        foreach ($allRelatedProductsRelations as $relation) {
            $relatedProducts[$relation->feed_id][] = $products[$relation->entity_id];
        }

        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());

    }

    /**
     * @return array
     * Достаем массив закрепённых продуктов не для выгрузки
     * @throws \Exception
     */
    public function getAllNotRelatedProducts()
    {
        $allNotRelatedProductsRelations = $this->relationsEntity->find([
            'limit' => $this->relationsEntity->count(),
            'entity_type' => 'product',
            'include' => 0
        ]);

        $notRelatedProductsIds = [];
        foreach ($allNotRelatedProductsRelations as $relation) {
            $notRelatedProductsIds[] = $relation->entity_id;
        }

        $products = $this->productsHelper->getList(['id' => $notRelatedProductsIds]);

        $notRelatedProducts = [];
        foreach ($allNotRelatedProductsRelations as $relation) {
            $notRelatedProducts[$relation->feed_id][] = $products[$relation->entity_id];
        }

        return ExtenderFacade::execute(__METHOD__, $notRelatedProducts, func_get_args());
    }
}