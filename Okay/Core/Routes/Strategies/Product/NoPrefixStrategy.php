<?php


namespace Okay\Core\Routes\Strategies\Product;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\ProductsEntity;

class NoPrefixStrategy extends AbstractRouteStrategy
{

    /** @var ProductsEntity */
    private $productsEntity;
    
    private $mockRouteParams = ['{$url}/?{$variantId}', ['{$url}' => '', '{$variantId}' => '(\d*)'], []];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
    }
    
    public function generateRouteParams($url) : array
    {
        list($productUrl, $variantId) = $this->matchProductUrlFromUri($url);
        $productId = $this->productsEntity->col('id')->get((string) $productUrl);

        if (empty($productId)) {
            return $this->mockRouteParams;
        }

        return [
            '{$url}/?{$variantId}',
            [
                '{$url}' => $productUrl,
                '{$variantId}' => $variantId,
            ],
            [
                '{$url}' => $productUrl,
                '{$variantId}' => $variantId,
            ]
        ];
    }

    private function matchProductUrlFromUri($url) : array
    {
        $urlParams = explode('/', trim($url, '/'));
        return array_pad($urlParams, 2, '');
    }
}