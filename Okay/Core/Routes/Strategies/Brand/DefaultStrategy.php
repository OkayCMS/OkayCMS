<?php


namespace Okay\Core\Routes\Strategies\Brand;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\Settings;
use Okay\Core\ServiceLocator;
use Okay\Entities\BrandsEntity;

class DefaultStrategy extends AbstractRouteStrategy
{
    /** @var Settings */
    private $settings;

    /** @var BrandsEntity */
    private $brandsEntity;

    private $mockRouteParams;


    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();

        /** @var EntityFactory $entityFactory */
        $entityFactory = $serviceLocator->getService(EntityFactory::class);

        $this->settings = $serviceLocator->getService(Settings::class);

        $this->brandsEntity = $entityFactory->get(BrandsEntity::class);

        $this->mockRouteParams = [
            '/'.$this->settings->get('brand_routes_template__default').'/{$url}/?{$filtersUrl}', [
                '{$url}' => ' ', '{$filtersUrl}' => '(.*)'
            ],
            []
        ];
    }

    public function generateRouteParams($url)
    {
        if (empty($prefix = $this->settings->get('brand_routes_template__default'))) {
            $prefix = 'brand';
        }

        $url = $this->matchBrandUrlFromUri($url, $prefix);

        $brandId = $this->brandsEntity->col('id')->find(['url' => $url]);

        if (empty($brandId)) {
            return $this->mockRouteParams;
        }

        return [
            '/'.$prefix.'/{$url}/?{$filtersUrl}', [
                '{$url}' => '([^/]*)', '{$filtersUrl}' => '(.*)'
            ],
            []
        ];
    }

    private function matchBrandUrlFromUri($url, $prefix) : ?string
    {
        preg_match("/^{$prefix}\/([^\/]*)/", $url, $matches);

        return $matches[1] ?? null;
    }
}