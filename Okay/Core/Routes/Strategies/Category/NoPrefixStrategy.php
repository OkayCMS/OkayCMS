<?php


namespace Okay\Core\Routes\Strategies\Category;

use Okay\Core\EntityFactory;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\CategoriesEntity;

class NoPrefixStrategy extends AbstractRouteStrategy
{
    /**
     * @var CategoriesEntity
     */
    private $categoriesEntity;

    private $mockRouteParams = ['{$url}{$filtersUrl}', ['{$url}' => '', '{$filtersUrl}' => ''], ['{$url}' => '', '{$filtersUrl}' => '']];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
    }

    public function generateRouteParams($url)
    {
        $categoryUrl = $this->matchCategoryUrl($url);
        $category    = $this->categoriesEntity->get((string) $categoryUrl);

        if (empty($category)) {
            return $this->mockRouteParams;
        }

        return [
            '{$url}{$filtersUrl}',
            [
                '{$url}' => $categoryUrl,
                '{$filtersUrl}' => '/'.$this->matchFiltersUrl($categoryUrl, $url)
            ],
            [
                '{$url}' => $categoryUrl,
                '{$filtersUrl}' => $this->matchFiltersUrl($categoryUrl, $url)
            ]
        ];
    }

    private function matchCategoryUrl($url)
    {
        preg_match("~(?:category_features/)?([^/]+)~ui", $url, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }

    private function matchFiltersUrl($categoryUrl, $url)
    {
        if (strpos($url, 'category_features') !== false) {
            $url = substr($url, strlen('category_features') + 1);
        }
        return substr($url, strlen($categoryUrl) + 1);
    }
}