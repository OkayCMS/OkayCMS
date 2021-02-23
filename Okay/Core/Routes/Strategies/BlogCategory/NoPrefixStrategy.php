<?php


namespace Okay\Core\Routes\Strategies\BlogCategory;

use Okay\Core\EntityFactory;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\BlogCategoriesEntity;

class NoPrefixStrategy extends AbstractRouteStrategy
{
    /** @var BlogCategoriesEntity */
    private $categoriesEntity;

    private $mockRouteParams = ['{$url}', ['{$url}' => ''], ['{$url}' => '']];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->categoriesEntity = $entityFactory->get(BlogCategoriesEntity::class);
    }

    public function generateRouteParams($url)
    {
        $categoryUrl = $this->matchCategoryUrl($url);
        $category    = $this->categoriesEntity->get((string) $categoryUrl);

        if (empty($category)) {
            return $this->mockRouteParams;
        }

        return [
            '{$url}',
            [
                '{$url}' => $categoryUrl,
            ],
            [
                '{$url}' => $categoryUrl,
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

}