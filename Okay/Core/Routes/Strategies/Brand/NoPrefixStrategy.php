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

    private $mockRouteParams = ['{$url}', ['{$url}' => ''], ['{$url}' => '']];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->brandsEntity = $entityFactory->get(BrandsEntity::class);
    }

    public function generateRouteParams($url)
    {
        $brandUrl = $this->matchCategoryUrl($url);
        $brand    = $this->brandsEntity->get((string) $brandUrl);

        if (empty($brand)) {
            return $this->mockRouteParams;
        }

        return [
            '{$url}{$filtersUrl}',
            [
                '{$url}' => $brandUrl,
                '{$filtersUrl}' => '/'.$this->matchFiltersUrl($brandUrl, $url)
            ],
            [
                '{$url}' => $brandUrl,
                '{$filtersUrl}' => $this->matchFiltersUrl($brandUrl, $url)
            ]
        ];
    }

    private function matchCategoryUrl($url)
    {
        preg_match("/([^\/]+)/ui", $url, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }

    private function matchFiltersUrl($brandUrl, $url)
    {
        return substr($url, strlen($brandUrl) + 1);
    }
}