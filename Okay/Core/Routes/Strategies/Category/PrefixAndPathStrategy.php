<?php


namespace Okay\Core\Routes\Strategies\Category;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\CategoryRoute;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\RouterCacheEntity;
use Psr\Log\LoggerInterface;

class PrefixAndPathStrategy extends AbstractRouteStrategy
{
    /** @var Settings */
    private $settings;

    /** @var CategoriesEntity */
    private $categoriesEntity;

    /** @var RouterCacheEntity */
    private $cacheEntity;

    /** @var LoggerInterface */
    private $logger;

    // Сообщаем что данная стратегия может использовать sql для формирования урла
    protected $isUsesSqlToGenerate = true;

    public function __construct()
    {
        $serviceLocator         = ServiceLocator::getInstance();
        $this->settings         = $serviceLocator->getService(Settings::class);
        $this->logger           = $serviceLocator->getService(LoggerInterface::class);
        $entityFactory          = $serviceLocator->getService(EntityFactory::class);
        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        $this->cacheEntity      = $entityFactory->get(RouterCacheEntity::class);
    }

    public function generateSlugUrl($url)
    {
        if (empty($url)) {
            return '';
        } elseif ($route = CategoryRoute::getUrlSlugAlias($url)) {// Может уже указали для этого урла его slug
            return $route;
        } elseif (CategoryRoute::getUseSqlToGenerate() === false) {// Если запретили выполнять запросы для генерации урла
            $this->logger->notice('For generate route to category "'.$url.'" need execute SQL query. Or set url through "Okay\Core\Routes\CategoryRoute::setUrlSlugAlias()"');
            return '';
        }

        if ($slug = $this->cacheEntity->cols(['slug_url'])->findOne(['type' => 'category', 'url' => $url])) {
            return $slug;
        }
        $category = $this->categoriesEntity->get((string) $url);
        $slug = trim($category->path_url, '/');

        // Запоминаем в оперативке slug для этого урла
        CategoryRoute::setUrlSlugAlias($url, $slug);

        $this->cacheEntity->add([
            'url' => $url,
            'slug_url' => $slug,
            'type' => 'category',
        ]);

        return $slug;
    }

    public function generateRouteParams($url)
    {
        $prefix = $this->settings->get('category_routes_template__prefix_and_path') . '/';
        $allCategories = $this->categoriesEntity->find();

        $categoriesPathUrls = [];
        foreach($allCategories as $category) {
            $categoriesPathUrls[] = $category->path_url;
        }

        // Сортируем урлы категорий по длине, от большей к меньшей
        usort($categoriesPathUrls, function($a, $b) {
            $difference =  strlen($b) - strlen($a);
            return $difference ?: strcmp($a, $b);
        });
        
        $matchedRoute = null;
        foreach ($categoriesPathUrls as $categoryPathUrl) {
            $urlPath = trim($categoryPathUrl, '/');
            if ($this->compareUrlStartsNoSuccess($prefix.$urlPath, $url)) {
                continue;
            }

            $urlParts = explode('/', $urlPath);
            $lastPart = array_pop($urlParts);
            $pathPrefix = '';
            if (!empty($urlParts)) {
                $pathPrefix = implode('/', $urlParts) . '/';
            }
            $filter = trim($this->matchFiltersUrl($prefix.$urlPath, $url), '/');
            $matchedRoute = [
                $prefix.'{$url}{$filtersUrl}',
                [
                    '{$url}' => "{$pathPrefix}({$lastPart})",
                    '{$filtersUrl}' => "/?(" . $filter . ")",
                ],
                []
            ];
            break;
        }

        if (empty($matchedRoute)) {
            return $this->getMockRouteParams($prefix);
        }

        return $matchedRoute;
    }

    private function getMockRouteParams($prefix)
    {
        return [$prefix.'{$url}{$filtersUrl}', ['{$url}' => '', '{$filtersUrl}' => ''], []];
    }

    private function compareUrlStartsNoSuccess($categoryPathUrl, $url)
    {

        if (strpos($url, 'category_features') !== false) {
            $url = substr($url, strlen('category_features') + 1);
        }
        
        $categoryPathUrl = ltrim($categoryPathUrl, '/');
        $compareAccessUri = substr($url, 0, strlen($categoryPathUrl));
        return $categoryPathUrl !== $compareAccessUri;
    }

    private function matchFiltersUrl($categoryPathUrl, $url)
    {
        if (strpos($url, 'category_features') !== false) {
            $url = substr($url, strlen('category_features') + 1);
        }
        
        return substr($url, strlen($categoryPathUrl) + 1);
    }
}