<?php


namespace Okay\Core\Routes\Strategies\BlogCategory;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\BlogCategoryRoute;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\RouterCacheEntity;
use Psr\Log\LoggerInterface;

class PrefixAndPathStrategy extends AbstractRouteStrategy
{
    /** @var Settings */
    private $settings;

    /** @var BlogCategoriesEntity */
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
        $this->categoriesEntity = $entityFactory->get(BlogCategoriesEntity::class);
        $this->cacheEntity      = $entityFactory->get(RouterCacheEntity::class);
    }

    public function generateSlugUrl($url)
    {
        if (empty($url)) {
            return '';
        } elseif ($route = BlogCategoryRoute::getUrlSlugAlias($url)) {// Может уже указали для этого урла его slug
            return $route;
        } elseif (BlogCategoryRoute::getUseSqlToGenerate() === false) {// Если запретили выполнять запросы для генерации урла
            $this->logger->notice('For generate route to category "'.$url.'" need execute SQL query. Or set url through "Okay\Core\Routes\CategoryRoute::setUrlSlugAlias()"');
            return '';
        }

        if ($slug = $this->cacheEntity->cols(['slug_url'])->findOne(['type' => 'blog_category', 'url' => $url])) {
            return $slug;
        }
        $category = $this->categoriesEntity->get((string) $url);
        $slug = trim($category->path_url, '/');

        // Запоминаем в оперативке slug для этого урла
        BlogCategoryRoute::setUrlSlugAlias($url, $slug);

        $this->cacheEntity->add([
            'url' => $url,
            'slug_url' => $slug,
            'type' => 'blog_category',
        ]);

        return $slug;
    }

    public function generateRouteParams($url)
    {
        $prefix = $this->settings->get('blog_category_routes_template__prefix_and_path') . '/';
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
            
            $matchedRoute = [
                $prefix.'{$url}',
                [
                    '{$url}' => "{$pathPrefix}({$lastPart})",
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
        return [$prefix.'{$url}', ['{$url}' => ''], []];
    }

    private function compareUrlStartsNoSuccess($categoryPathUrl, $url)
    {
        $categoryPathUrl = ltrim($categoryPathUrl, '/');
        $compareAccessUri = substr($url, 0, strlen($categoryPathUrl));
        return $categoryPathUrl !== $compareAccessUri;
    }
}