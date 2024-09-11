<?php


namespace Okay\Core\Routes\Strategies\Brand;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\BrandsEntity;

class NoPrefixStrategy extends AbstractRouteStrategy
{
    /**
     * @var BrandsEntity
     */
    private $brandsEntity;

    private $mockRouteParams = ['/{$url}/?{$filtersUrl}', ['{$url}' => '', '{$filtersUrl}' => ''], ['{$url}' => '', '{$filtersUrl}' => '']];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->brandsEntity = $entityFactory->get(BrandsEntity::class);
    }

    public function generateRouteParams($url)
    {
        $brandUrl = $this->matchBrandUrl($url);
        $brand    = $this->brandsEntity->get((string) $brandUrl);

        if (empty($brand)) {
            return $this->mockRouteParams;
        }

        $filterUrl = $this->matchFiltersUrl($brandUrl, $url);

        return [
            '/{$url}/?{$filtersUrl}',
            [
                '{$url}' => $brandUrl,
                '{$filtersUrl}' => $filterUrl
            ],
            [
                '{$url}' => $brandUrl,
                '{$filtersUrl}' => $filterUrl
            ]
        ];
    }

    private function matchBrandUrl($url)
    {
        preg_match("~(?:brand_features/?)?([^/]+)~ui", $url, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }

    private function matchFiltersUrl($brandUrl, $url)
    {
        if (strpos($url, 'brand_features') !== false) {
            $url = substr($url, strlen('brand_features') + 1);
        }

        return substr($url, strlen($brandUrl) + 1);
    }
}