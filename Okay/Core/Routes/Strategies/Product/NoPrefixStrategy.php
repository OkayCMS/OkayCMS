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

    /** @var array{string, array<string, string>, array<string, string>} */
    private $mockRouteParams = ['{$url}/?{$variantId}', ['{$url}' => '', '{$variantId}' => ''], []];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
    }

    /**
     * @param string $url
     *
     * @return array{string, array<string, string>, array<string, string>}
     */
    public function generateRouteParams($url): array
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

    /**
     * @param string $url
     *
     * @return list{string, string}
     */
    private function matchProductUrlFromUri($url): array
    {
        $urlParams = explode('/', trim($url, '/'));
        return [
            $urlParams[0],
            $urlParams[1] ?? '',
        ];
    }
}
