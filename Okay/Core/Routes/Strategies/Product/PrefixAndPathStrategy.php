<?php


namespace Okay\Core\Routes\Strategies\Product;


use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\QueryFactory;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\RouterCacheEntity;
use Psr\Log\LoggerInterface;

class PrefixAndPathStrategy extends AbstractRouteStrategy
{
    /** @var Database */
    private $db;

    /** @var ProductsEntity */
    private $productsEntity;

    /** @var CategoriesEntity */
    private $categoriesEntity;

    /** @var Settings */
    private $settings;

    /** @var LoggerInterface */
    private $logger;

    /** @var RouterCacheEntity */
    private $cacheEntity;
    
    /** @var QueryFactory */
    private $queryFactory;

    // Сообщаем что данная стратегия может использовать sql для формирования урла
    protected $isUsesSqlToGenerate = true;

    public function __construct()
    {
        $serviceLocator         = ServiceLocator::getInstance();
        $entityFactory          = $serviceLocator->getService(EntityFactory::class);
        $this->db               = $serviceLocator->getService(Database::class);
        $this->queryFactory     = $serviceLocator->getService(QueryFactory::class);
        $this->settings         = $serviceLocator->getService(Settings::class);
        $this->logger           = $serviceLocator->getService(LoggerInterface::class);
        $this->productsEntity   = $entityFactory->get(ProductsEntity::class);
        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        $this->cacheEntity      = $entityFactory->get(RouterCacheEntity::class);
    }

    public function generateSlugUrl($url) : string
    {
        if (empty($url)) {
            return '';
        } elseif ($route = ProductRoute::getUrlSlugAlias($url)) {// Может уже указали для этого урла его slug
            return $route;
        } elseif (ProductRoute::getUseSqlToGenerate() === false) {// Если запретили выполнять запросы для генерации урла
            $this->logger->notice('For generate route to product "'.$url.'" need execute SQL query. Or set url through "Okay\Core\Routes\ProductRoute::setUrlSlugAlias()"');
            return '';
        }
        
        if ($slug = $this->cacheEntity->cols(['slug_url'])->findOne(['type' => 'product', 'url' => $url])) {
            return $slug;
        }
        
        $product = $this->productsEntity->get((string) $url);
        $slug = $product->url;
        if (empty($product->main_category_id)) {
            $this->logger->warning('Missing "main_category_id" for product "'.$url.'"');
        } else {
            $category = $this->categoriesEntity->get((int) $product->main_category_id);
            $slug = substr($category->path_url, 1).'/'.$product->url;
        }

        // Запоминаем в оперативке slug для этого урла
        ProductRoute::setUrlSlugAlias($url, $slug);
        
        // Сохраняем в базу slug, чтобы его больше не генерить
        $this->cacheEntity->add([
            'url' => $url,
            'slug_url' => $slug,
            'type' => 'product',
        ]);
        
        return $slug;
    }

    public function generateRouteParams($url) : array
    {
        $prefix = $this->getPrefix();
        if ($this->prefixIsFailed($prefix, $url)) {
            return $this->getMockRouteParams($prefix);
        }

        $noPrefixUrl            = ltrim(substr($url, strlen($prefix) + 1), '/');
        $matchedCategories      = $this->matchCategories($noPrefixUrl);
        $mappedParentCategories = $this->mapCategoriesByParents($matchedCategories);

        if (empty($mappedParentCategories)) {
            return $this->getMockRouteParams($prefix);
        }

        $mainCategoryId = $this->findMostNestedCategoryId($mappedParentCategories);
        $category   = $this->categoriesEntity->get((int) $mainCategoryId);
        $category->path_url = ltrim($category->path_url, '/');

        if ($this->uriNoContainsValidCategoryPathUrl($noPrefixUrl, $category->path_url)) {
            return $this->getMockRouteParams($prefix);
        }

        list($productUrl, $variantId) = $this->matchProductUrlFromUri($noPrefixUrl, $category->path_url);
        $productId    = $this->productsEntity->col('id')->findOne([
            'url'              => $productUrl,
            'main_category_id' => $mainCategoryId
        ]);
        $productPath = $category->path_url . '/' . $productUrl;

        if (empty($productId)) {
            return $this->getMockRouteParams($prefix);
        }

        return [
            $prefix.'/{$url}/?{$variantId}',
            [
                '{$url}' => $productPath,
                '{$variantId}' => $variantId,
            ],
            [
                '{$url}' => $productUrl,
                '{$variantId}' => $variantId,
            ]
        ];
    }

    private function getMockRouteParams($prefix) : array
    {
        return [$prefix.'/{$url}/?{$variantId}', ['{$url}' => '', '{$variantId}' => '(\d*)'], []];
    }

    private function matchProductUrlFromUri($url, $categoryPathUrl)
    {
        $noCategoryPathUri = substr($url, strlen($categoryPathUrl));

        if ($noCategoryPathUri === '/') {
            $noCategoryPathUri = substr($noCategoryPathUri, 1);
        }

        $urlParams = explode('/', trim($noCategoryPathUri, '/'));

        // Здесь остался только урл товара и возможно id варианта, если еще что-то - бросаем 404
        if (count($urlParams) > 2) {
            return false;
        }

        return array_pad($urlParams, 2, '');
    }

    private function uriNoContainsValidCategoryPathUrl($url, $categoryPathUrl) : bool
    {
        $comparePartUri = substr($url, 0, strlen($categoryPathUrl));
        return $comparePartUri !== $categoryPathUrl;
    }

    private function matchCategories($noPrefixUri) : array
    {
        $parts = explode('/', $noPrefixUri);

        $select = $this->queryFactory->newSelect();
        $select->cols(['id', 'parent_id', 'url'])
            ->from(CategoriesEntity::getTable())
            ->where('url IN(:urls)')
            ->bindValue('urls', $parts);
        $this->db->query($select);
        return $this->db->results(null, 'id');
    }

    private function findMostNestedCategoryId($mappedByParentCategories)
    {
        $sortCategories = function($category) use (&$sortCategories, $mappedByParentCategories) {
            $nestedSortCategories[] = $category;

            if (empty($mappedByParentCategories[$category->id])) {
                return $category;
            }

            return $sortCategories($mappedByParentCategories[$category->id]);
        };

        $mostNestedCategory = $sortCategories(reset($mappedByParentCategories));
        return $mostNestedCategory->id;
    }

    private function mapCategoriesByParents($categories)
    {
        $categoriesMappedByParent = [];
        foreach($categories as $category) {
            if (isset($categoriesMappedByParent[$category->parent_id])) {
                return false;
            }
            $categoriesMappedByParent[$category->parent_id] = $category;
        }

        return array_values($categoriesMappedByParent);
    }

    private function prefixIsFailed($prefix, $url) : bool
    {
        return $prefix !== substr($url, 0, strlen($prefix));
    }

    private function getPrefix() : string
    {
        $prefix = $this->settings->get('product_routes_template__prefix_and_path');

        if (empty($prefix)) {
            return 'catalog';
        }

        return $prefix;
    }
}