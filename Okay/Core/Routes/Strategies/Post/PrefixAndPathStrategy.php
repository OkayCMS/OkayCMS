<?php


namespace Okay\Core\Routes\Strategies\Post;


use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\QueryFactory;
use Okay\Core\Routes\PostRoute;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\BlogEntity;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\RouterCacheEntity;
use Psr\Log\LoggerInterface;

class PrefixAndPathStrategy extends AbstractRouteStrategy
{
    /** @var Database */
    private $db;

    /** @var BlogEntity */
    private $blogEntity;

    /** @var BlogCategoriesEntity */
    private $categoriesEntity;

    /** @var Settings */
    private $settings;

    /** @var LoggerInterface */
    private $logger;

    /** @var RouterCacheEntity */
    private $cacheEntity;
    
    /** @var QueryFactory */
    private $queryFactory;

    // Сообщаем что данная стратегия может использовать sql для формирования урла
    protected $isUsesSqlToGenerate = true;

    public function __construct()
    {
        $serviceLocator         = ServiceLocator::getInstance();
        $entityFactory          = $serviceLocator->getService(EntityFactory::class);
        $this->db               = $serviceLocator->getService(Database::class);
        $this->queryFactory     = $serviceLocator->getService(QueryFactory::class);
        $this->settings         = $serviceLocator->getService(Settings::class);
        $this->logger           = $serviceLocator->getService(LoggerInterface::class);
        $this->blogEntity       = $entityFactory->get(BlogEntity::class);
        $this->categoriesEntity = $entityFactory->get(BlogCategoriesEntity::class);
        $this->cacheEntity      = $entityFactory->get(RouterCacheEntity::class);
    }

    public function generateSlugUrl($url)
    {
        if (empty($url)) {
            return '';
        } elseif ($route = PostRoute::getUrlSlugAlias($url)) {// Может уже указали для этого урла его slug
            return $route;
        } elseif (PostRoute::getUseSqlToGenerate() === false) {// Если запретили выполнять запросы для генерации урла
            $this->logger->notice('For generate route to post "'.$url.'" need execute SQL query. Or set url through "Okay\Core\Routes\PostRoute::setUrlSlugAlias()"');
            return '';
        }
        
        if ($slug = $this->cacheEntity->cols(['slug_url'])->findOne(['type' => 'post', 'url' => $url])) {
            return $slug;
        }
        
        $post = $this->blogEntity->findOne(['url' => $url]);
        $slug = $post->url;
        if (empty($post->main_category_id)) {
            $this->logger->warning('Missing "main_category_id" for post "'.$url.'"');
        } else {
            $category = $this->categoriesEntity->findOne(['id' => $post->main_category_id]);
            $slug = substr($category->path_url, 1).'/'.$post->url;
        }
        
        // Запоминаем в оперативке slug для этого урла
        PostRoute::setUrlSlugAlias($url, $slug);
        
        // Сохраняем в базу slug, чтобы его больше не генерить
        $this->cacheEntity->add([
            'url' => $url,
            'slug_url' => $slug,
            'type' => 'post',
        ]);
        
        return $slug;
    }

    public function generateRouteParams($url)
    {
        $prefix = $this->getPrefix();
        
        if ($this->prefixIsFailed($prefix, $url)) {
            return $this->getMockRouteParams($prefix);
        }

        $noPrefixUrl            = substr($url, strlen($prefix) + 1);
        $matchedCategories      = $this->matchCategories($noPrefixUrl);
        $mappedParentCategories = $this->mapCategoriesByParents($matchedCategories);

        if (empty($mappedParentCategories)) {
            return $this->getMockRouteParams($prefix);
        }

        $mainCategoryId = $this->findMostNestedCategoryId($mappedParentCategories);
        $category = $this->categoriesEntity->findOne(['id' => $mainCategoryId]);

        if ($this->uriNoContainsValidCategoryPathUrl($noPrefixUrl, $category->path_url)) {
            return $this->getMockRouteParams($prefix);
        }

        $postUrl = $this->matchPostUrlFromUri($noPrefixUrl, $category->path_url);
        $post    = $this->blogEntity->findOne([
            'url'              => $postUrl,
            'main_category_id' => $mainCategoryId
        ]);

        if (empty($post)) {
            return $this->getMockRouteParams($prefix);
        }

        return [
            $prefix.'/{$url}',
            [
                '{$url}' => $noPrefixUrl
            ],
            [
                '{$url}' => $postUrl
            ]
        ];
    }

    private function getMockRouteParams($prefix)
    {
        return [$prefix.'/{$url}', ['{$url}' => ''], []];
    }

    private function matchPostUrlFromUri($url, $categoryPathUrl)
    {
        $noCategoryPathUri = substr($url, strlen($categoryPathUrl));

        if ($noCategoryPathUri === '/') {
            $noCategoryPathUri = substr($noCategoryPathUri, 1);
        }

        $urlParams = explode('/', $noCategoryPathUri);

        // Здесь остался только урл поста и если после урла поста еще что-то есть, бросаем 404
        if (!empty($urlParams[1])) {
            return false;
        }

        return $urlParams[0];
    }

    private function uriNoContainsValidCategoryPathUrl($url, $categoryPathUrl)
    {
        if ($url[0] !== '/') {
            $url = '/'.$url;
        }

        $comparePartUri = substr($url, 0, strlen($categoryPathUrl));

        return $comparePartUri !== $categoryPathUrl;
    }

    private function matchCategories($noPrefixUri)
    {
        $parts = explode('/', $noPrefixUri);

        $select = $this->queryFactory->newSelect();
        $select->cols(['id', 'parent_id', 'url'])
            ->from(BlogCategoriesEntity::getTable())
            ->where('url IN(:urls)')
            ->bindValue('urls', $parts);
        $this->db->query($select);
        return $this->db->results(null, 'id');
    }

    private function findMostNestedCategoryId($mappedByParentCategories)
    {
        $sortCategories = function($category) use (&$sortCategories, $mappedByParentCategories) {
            $nestedSortCategories[] = $category;

            if (empty($mappedByParentCategories[$category->id])) {
                return $category;
            }

            return $sortCategories($mappedByParentCategories[$category->id]);
        };

        $mostNestedCategory = $sortCategories($mappedByParentCategories[0]);
        return $mostNestedCategory->id;
    }

    private function mapCategoriesByParents($categories)
    {
        $categoriesMappedByParent = [];
        foreach($categories as $category) {
            if (isset($categoriesMappedByParent[$category->parent_id])) {
                return false;
            }
            $categoriesMappedByParent[$category->parent_id] = $category;
        }

        return $categoriesMappedByParent;
    }

    private function prefixIsFailed($prefix, $url)
    {
        return $prefix !== substr($url, 0, strlen($prefix));
    }

    private function getPrefix()
    {
        $prefix = $this->settings->get('post_routes_template__prefix_and_path');

        if (empty($prefix)) {
            return 'post';
        }

        return $prefix;
    }
}