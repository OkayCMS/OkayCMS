<?php


namespace Okay\Core\Routes\Strategies\Post;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\PostRoute;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\BlogEntity;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\RouterCacheEntity;
use Psr\Log\LoggerInterface;

class NoPrefixAndCategoryStrategy extends AbstractRouteStrategy
{
    /** @var BlogEntity */
    private $blogEntity;

    /** @var BlogCategoriesEntity */
    private $categoriesEntity;

    /** @var LoggerInterface */
    private $logger;

    /** @var RouterCacheEntity */
    private $cacheEntity;

    // Сообщаем что данная стратегия может использовать sql для формирования урла
    protected $isUsesSqlToGenerate = true;

    private $mockRouteParams = ['{$url}', ['{$url}' => ''], []];

    public function __construct()
    {
        $serviceLocator         = ServiceLocator::getInstance();
        $entityFactory          = $serviceLocator->getService(EntityFactory::class);
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
            $slug = $category->url.'/'.$post->url;
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
        $url = rtrim($url, '/');
        $parts = explode('/', $url);

        if (count($parts) != 2) {
            return $this->mockRouteParams;
        }

        list($categoryUrl, $postUrl) = $parts;

        $category = $this->categoriesEntity->findOne(['url' => $categoryUrl]);
        if (empty($category)) {
            return $this->mockRouteParams;
        }

        $post = $this->blogEntity->findOne(['url' => $postUrl]);
        if (empty($post) || $category->id !== $post->main_category_id) {
            return $this->mockRouteParams;
        }

        return [
            '{$url}',
            [
                '{$url}' => $url
            ],
            [
                '{$url}' => $postUrl
            ]
        ];
    }
}