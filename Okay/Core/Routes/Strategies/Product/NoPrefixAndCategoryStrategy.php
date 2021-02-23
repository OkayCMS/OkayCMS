<?php


namespace Okay\Core\Routes\Strategies\Product;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\RouterCacheEntity;
use Psr\Log\LoggerInterface;

class NoPrefixAndCategoryStrategy extends AbstractRouteStrategy
{
    /** @var ProductsEntity */
    private $productsEntity;

    /** @var CategoriesEntity */
    private $categoriesEntity;

    /** @var LoggerInterface */
    private $logger;

    /** @var RouterCacheEntity */
    private $cacheEntity;

    // Сообщаем что данная стратегия может использовать sql для формирования урла
    protected $isUsesSqlToGenerate = true;

    private $mockRouteParams = ['{$url}/?{$variantId}', ['{$url}' => '', '{$variantId}' => '(\d*)'], []];

    public function __construct()
    {
        $serviceLocator         = ServiceLocator::getInstance();
        $entityFactory          = $serviceLocator->getService(EntityFactory::class);
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

        $product  = $this->productsEntity->get((string) $url);
        $slug = $product->url;
        if (empty($product->main_category_id)) {
            $this->logger->warning('Missing "main_category_id" for product "'.$url.'"');
        } else {
            $category = $this->categoriesEntity->get((int) $product->main_category_id);
            $slug = $category->url.'/'.$product->url;
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
        $url = rtrim($url, '/');
        $parts = explode('/', $url);

        if (($partsNum = count($parts)) < 2 || $partsNum > 3) {
            return $this->mockRouteParams;
        }

        list($categoryUrl, $productUrl, $variantId) = array_pad($parts, 3, '');

        $category = $this->categoriesEntity->get((string) $categoryUrl);
        if (empty($category)) {
            return $this->mockRouteParams;
        }

        $product  = $this->productsEntity->cols(['id', 'main_category_id'])->get((string) $productUrl);
        if (empty($product) || $category->id !== $product->main_category_id) {
            return $this->mockRouteParams;
        }

        $productPath = $category->url . '/' . $productUrl;

        return [
            '{$url}/?{$variantId}',
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
}