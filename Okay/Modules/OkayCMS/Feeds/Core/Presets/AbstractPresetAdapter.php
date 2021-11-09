<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets;

use Aura\Sql\ExtendedPdo;
use Okay\Core\Database;
use Okay\Core\Design;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Money;
use Okay\Core\QueryFactory;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\RouterCacheEntity;
use Okay\Entities\VariantsEntity;
use Okay\Helpers\XmlFeedHelper;
use Okay\Modules\OkayCMS\Feeds\Core\InheritedExtenderTrait;
use Okay\Modules\OkayCMS\Feeds\Entities\ConditionsEntity;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;
use Okay\Modules\OkayCMS\Feeds\Init\Init;

abstract class AbstractPresetAdapter implements PresetAdapterInterface
{
    use InheritedExtenderTrait;

    /** @var string */
    static protected $headerTemplate;

    /** @var string */
    static protected $footerTemplate;


    /** @var Design */
    protected $design;

    /** @var Money */
    protected $money;

    /** @var QueryFactory */
    protected $queryFactory;

    /** @var Database */
    protected $database;

    /** @var ExtendedPdo */
    protected $pdo;

    /** @var Response */
    protected $response;

    /** @var XmlFeedHelper */
    protected $xmlFeedHelper;

    /** @var Settings */
    protected $settings;

    /** @var Languages */
    protected $languages;

    /** @var Image */
    protected $image;


    /** @var CurrenciesEntity */
    protected $currenciesEntity;

    /** @var FeedsEntity */
    protected $feedsEntity;

    /** @var CategoriesEntity */
    protected $categoriesEntity;


    /** @var object */
    protected $mainCurrency;

    /** @var array */
    protected $allCurrencies;

    /** @var */
    protected $allCategories;

    /** @var object */
    protected $feed;

    public function __construct(
        Money            $money,
        Design           $design,
        QueryFactory     $queryFactory,
        Database         $database,
        XmlFeedHelper    $xmlFeedHelper,
        Response         $response,
        ExtendedPdo      $pdo,
        Settings         $settings,
        Languages        $languages,
        Image            $image,
        CurrenciesEntity $currenciesEntity,
        FeedsEntity      $feedsEntity,
        CategoriesEntity $categoriesEntity
    ) {
        $this->money         = $money;
        $this->design        = $design;
        $this->queryFactory  = $queryFactory;
        $this->database      = $database;
        $this->xmlFeedHelper = $xmlFeedHelper;
        $this->pdo           = $pdo;
        $this->response      = $response;
        $this->settings      = $settings;
        $this->languages     = $languages;
        $this->image         = $image;

        $this->currenciesEntity = $currenciesEntity;
        $this->feedsEntity      = $feedsEntity;
        $this->categoriesEntity = $categoriesEntity;

        $this->init();
    }

    protected function init(): void
    {
        $this->mainCurrency  = $this->currenciesEntity->getMainCurrency();
        $this->allCurrencies = $this->currenciesEntity->mappedBy('id')->find();

        $this->inheritedExtender(__FUNCTION__, null, func_get_args());
    }

    public function render($feed): void
    {
        $this->feed = $feed;

        $feed->settings = $this->loadSettings($feed->settings);

        if ($currencies = $this->currenciesEntity->find()) {
            $this->design->assign('main_currency', reset($currencies));

            foreach ($currencies as $c) {
                $this->money->setCurrency($c);
            }
        }

        $categories = $this->buildCategories($feed->id);
        $xmlCategories = $this->xmlFeedHelper->compileItems($categories);

        $this->design->assign('feed', $feed);
        $this->design->assign('xml_categories', $xmlCategories);

        $this->response->setContentType(RESPONSE_XML);
        $this->response->sendHeaders();
        $this->response->sendStream($this->design->fetch($this->getHeaderTemplate()));

        $this->allCategories = $this->categoriesEntity->mappedBy('id')->find();

        $productsQuery = $this->getQuery($feed->id);

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement('SET SQL_BIG_SELECTS=1');
        $this->database->query($sql);

        // Делаем это, чтобы не обновлять настройки, если такого ресайза нет
        $this->image->addImagesSize('1200x1200', 'product');

        // На всякий случай наполним кеш роутов
        Router::generateRouterCache();

        // Запрещаем выполнять запросы в БД во время генерации урла т.к. мы работаем с небуферизированными запросами
        ProductRoute::setNotUseSqlToGenerate();

        // Увеличиваем лимит ф-ции GROUP_CONCAT()
        $query = $this->queryFactory->newSqlQuery();
        $query->setStatement('SET SESSION group_concat_max_len = 1000000;')->execute();

        // Для экономии памяти работаем с небуферизированными запросами
        $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $prevProductId = null;
        while ($product = $productsQuery->result()) {
            $product = $this->xmlFeedHelper->attachFeatures($product);
            $product = $this->xmlFeedHelper->attachDescriptionByTemplate($product);
            $product = $this->xmlFeedHelper->attachProductImages($product);

            $addVariantUrl = false;
            if ($prevProductId === $product->product_id) {
                $addVariantUrl = true;
            }
            $prevProductId = $product->product_id;
            $item = $this->getItem($product, $addVariantUrl);
            $xmlProduct = $this->xmlFeedHelper->compileItems($item);
            $this->response->sendStream($xmlProduct);
        }

        $this->response->sendStream($this->design->fetch($this->getFooterTemplate()));

        $this->inheritedExtender(__FUNCTION__, null, func_get_args());
    }

    protected function getHeaderTemplate(): string
    {
        return $this->inheritedExtender(__FUNCTION__, static::$headerTemplate, func_get_args());
    }

    protected function getFooterTemplate(): string
    {
        return $this->inheritedExtender(__FUNCTION__, static::$footerTemplate, func_get_args());
    }

    protected function loadSettings(array $settings): array
    {
        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    protected function buildCategories($feedId): array
    {
        $categories = array_map([$this, 'buildCategory'], $this->categoriesEntity->find());

        $result = [
            [
                'tag' => 'categories',
                'data' => $categories
            ]
        ];

        return $this->inheritedExtender(__FUNCTION__, $result, func_get_args());
    }

    protected function buildCategory(object $dbCategory): array
    {
        $categorySettings = $this->getCategorySettings($dbCategory->id);

        if (!$categorySettings || !($name = $categorySettings['name_in_feed'])) {
            $name = $dbCategory->name;
        }

        $xmlCategory = [
            'tag' => 'category',
            'data' => $this->xmlFeedHelper->escape($name),
            'attributes' => [
                'id' => $dbCategory->id
            ],
        ];

        if (!empty($dbCategory->parent_id)) {
            $xmlCategory['attributes']['parentId'] = $dbCategory->parent_id;
        }

        return $this->inheritedExtender(__FUNCTION__, $xmlCategory, func_get_args());
    }

    /**
     * Метод возвращает итоговый запрос, который достаёт все товары с изображениями с свойствами.
     * По результатам замеров, лучшие результаты производительности достигаются при джоине ленговых таблиц
     * после фильтрации.
     * Фильтрация результатов и группировка свойств с изображениями вынесена в подзапрос,
     * который формируется методом getSubSelect()
     */
    protected function getQuery($feedId): Select
    {
        $subSelect = $this->getSubSelect(...func_get_args());

        $sql = $this->queryFactory->newSelect();
        $sql->cols([
                't.*',
                'lp.name as product_name',
                'lv.name as variant_name',
                'lb.name as brand_name'
            ])
            ->fromSubSelect($subSelect, 't')
            ->leftJoin(ProductsEntity::getLangTable().' AS lp', 'lp.product_id = t.product_id and lp.lang_id=' . $this->languages->getLangId())
            ->leftJoin(VariantsEntity::getLangTable().' AS lv', 'lv.variant_id = t.variant_id and lv.lang_id=' . $this->languages->getLangId())
            ->leftJoin(BrandsEntity::getLangTable().' AS lb', 'lb.brand_id = t.brand_id and lb.lang_id=' . $this->languages->getLangId());

        return $this->inheritedExtender(__FUNCTION__, $sql, func_get_args());
    }

    /**
     * Метод возвращает подзапрос, который фильтрует и сортирует результаты, здесь достаются только не мультиязычные
     * данные, кроме свойств. Свойства нужно доставать здесь, т.к. их группируем через GROUP_CONCAT()
     */
    protected function getSubSelect($feedId): Select
    {
        $sql = $this->queryFactory->newSelect();

        $variantsCountSubSelect = $this->queryFactory->newSelect()
            ->from(VariantsEntity::getTable())
            ->cols([
                'product_id',
                'COUNT(*) AS total_variants'
            ])
            ->groupBy(['product_id']);

        $sql->cols([
                'v.stock',
                'v.price',
                'v.sku',
                'v.currency_id',
                'v.id AS variant_id',
                'p.id AS product_id',
                'p.url',
                'r.slug_url',
                'p.main_category_id',
                'p.brand_id',
                'vc.total_variants'
            ])
            ->from(VariantsEntity::getTable() . ' AS v')
            ->leftJoin(ProductsEntity::getTable().' AS  p', 'v.product_id=p.id')
            ->leftJoin(RouterCacheEntity::getTable().' AS r', 'r.url = p.url AND r.type="product"')
            ->joinSubSelect('left', $variantsCountSubSelect, 'vc', 'vc.product_id = p.id')
            ->where('p.visible')
            ->bindValue('feed_id', $feedId)
            ->groupBy(['v.id'])
            ->orderBy(['p.position DESC']);

        $sql = $this->addSubSelectInclusions($sql, $feedId);
        $sql = $this->addSubSelectExclusions($sql, $feedId);

        $sql = $this->xmlFeedHelper->joinImages($sql);
        $sql = $this->xmlFeedHelper->joinFeatures($sql);

        return $this->inheritedExtender(__FUNCTION__, $sql, func_get_args());
    }

    /**
     * Добавляет к запросу условия включающие товары в выгрузку. Выгрузка формируется на основании суммы всех условий,
     * а не их пересечении
     */
    protected function addSubSelectInclusions(Select $sql, $feedId): Select
    {
        if ($includedCategoryIds = $this->getIncludedCategoryIds($feedId)) {
            $includedCategoriesFilter = "OR p.id IN (SELECT product_id FROM __products_categories WHERE category_id IN (:included_category_ids))";
            $sql->bindValue('included_category_ids', $includedCategoryIds);
        } else {
            $includedCategoriesFilter = '';
        }

        $sql->where("((SELECT COUNT(*) FROM ".ConditionsEntity::getTable()." WHERE feed_id = :feed_id AND entity = 'product' AND type = 'inclusion' AND all_entities) OR p.id IN ({$this->buildSelectFromEntitiesConditions('product', 'inclusion')}) OR
                p.brand_id IN ({$this->buildSelectFromEntitiesConditions('brand', 'inclusion')}) OR
                p.id IN (SELECT pfv.product_id FROM ".Init::CONDITIONS_ENTITIES_RELATION_TABLE." AS `ce`
                    LEFT JOIN ".ConditionsEntity::getTable()." AS `con` ON con.id = ce.condition_id
                    LEFT JOIN __products_features_values AS `pfv` ON pfv.value_id = ce.entity_id
                    WHERE con.feed_id = :feed_id AND
                    con.entity = 'feature_value' AND
                    con.type = 'inclusion')
                {$includedCategoriesFilter})");

        return $this->inheritedExtender(__FUNCTION__, $sql, func_get_args());
    }

    /**
     * Добавляет к запросу условия исключающие товары из выгрузки. Товар исключается, если удовлетворяет хотя бы одному из условий
     */
    protected function addSubSelectExclusions(Select $sql, $feedId): Select
    {
        $sql->where("!(SELECT COUNT(*) FROM ".ConditionsEntity::getTable()." WHERE feed_id = :feed_id AND entity = 'product' AND type = 'exclusion' AND all_entities) AND p.id NOT IN ({$this->buildSelectFromEntitiesConditions('product', 'exclusion')})")
            ->where("p.brand_id NOT IN ({$this->buildSelectFromEntitiesConditions('brand', 'exclusion')})")
            ->where("p.id NOT IN (
                        SELECT pfv.product_id
                        FROM ".Init::CONDITIONS_ENTITIES_RELATION_TABLE." AS `ce`
                        LEFT JOIN ".ConditionsEntity::getTable()." AS `con` ON con.id = ce.condition_id
                        LEFT JOIN __products_features_values AS `pfv` ON pfv.value_id = ce.entity_id
                        WHERE con.feed_id = :feed_id AND
                        con.entity = 'feature_value' AND
                        con.type = 'exclusion')");

        if ($excludedCategoryIds = $this->getExcludedCategoryIds($feedId)) {
            $sql->where("p.id NOT IN (
                SELECT product_id
                FROM __products_categories
                WHERE category_id IN (:excluded_category_ids))")
                ->bindValue('excluded_category_ids', $excludedCategoryIds);
        }

        return $this->inheritedExtender(__FUNCTION__, $sql, func_get_args());
    }

    /**
     * Получает список категорий, которые нужно включить в выгрузку. Делаем это на php, так как нужно строить дерево.
     */
    protected function getIncludedCategoryIds($feedId): array
    {
        $select = $this->queryFactory->newSelect();
        $select ->from(Init::CONDITIONS_ENTITIES_RELATION_TABLE.' AS ce')
            ->cols(['ce.entity_id'])
            ->join('LEFT', ConditionsEntity::getTable().' AS con', 'con.id = ce.condition_id')
            ->where("con.feed_id = :feed_id AND con.entity = 'category' AND con.type = 'inclusion'")
            ->bindValue('feed_id', $feedId);

        $includedCategoryIds = $select->results('entity_id');
        $includedCategoryIds = $this->xmlFeedHelper->addAllChildrenToList($includedCategoryIds);

        return $this->inheritedExtender(__FUNCTION__, $includedCategoryIds, func_get_args());
    }

    /**
     * Получает список категорий, которые нужно исключить из выгрузки. Делаем это на php, так как нужно строить дерево.
     */
    protected function getExcludedCategoryIds($feedId): array
    {
        $select = $this->queryFactory->newSelect();
        $select ->from(Init::CONDITIONS_ENTITIES_RELATION_TABLE.' AS ce')
            ->cols(['ce.entity_id'])
            ->join('LEFT', ConditionsEntity::getTable().' AS con', 'con.id = ce.condition_id')
            ->where("con.feed_id = :feed_id AND con.entity = 'category' AND con.type = 'exclusion'")
            ->bindValue('feed_id', $feedId);

        $excludedCategoryIds = $select->results('entity_id');
        $excludedCategoryIds = $this->xmlFeedHelper->addAllChildrenToList($excludedCategoryIds);

        return $this->inheritedExtender(__FUNCTION__, $excludedCategoryIds, func_get_args());
    }

    /**
     * Строит подзапрос для получения списка сущностей из таблицы условий в самой простой реализации.
     */
    protected function buildSelectFromEntitiesConditions(string $entity, string $type): string
    {
        $select = "SELECT ce.entity_id FROM ".Init::CONDITIONS_ENTITIES_RELATION_TABLE." AS `ce` LEFT JOIN ".ConditionsEntity::getTable()." AS `con` ON con.id = ce.condition_id WHERE con.feed_id = :feed_id AND con.entity = '{$entity}' AND con.type = '{$type}'";

        return $this->inheritedExtender(__FUNCTION__, $select, func_get_args());
    }

    /**
     * Получает название свойства используя таблицу сопоставления свойств
     */
    protected function getFeatureSettings($featureId)
    {
        $settings = $this->feed->features_settings[$featureId] ?? null;

        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    /**
     * Получает название свойства используя таблицу сопоставления свойств
     */
    protected function getCategorySettings($categoryId)
    {
        $settings = $this->feed->categories_settings[$categoryId] ?? null;

        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    /**
     * Формируем описание офера в виде массива
     *
     * @param object $product строка выборки из базы (запрос формирующийся методом getQuery),
     * но после отработки методов attachFeatures и attachImages.
     * @param bool $addVariantUrl Если true будет добавлен урл на определенный вариант
     */
    abstract protected function getItem(object $product, bool $addVariantUrl = false): array;
}