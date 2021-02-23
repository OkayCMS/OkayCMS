<?php


namespace Okay\Core\Routes\Strategies\Post;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Entities\BlogEntity;

class NoPrefixStrategy extends AbstractRouteStrategy
{

    /** @var BlogEntity */
    private $blogEntity;
    
    private $mockRouteParams = ['{$url}', ['{$url}' => ''], ['{$url}' => '']];

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $entityFactory  = $serviceLocator->getService(EntityFactory::class);

        $this->blogEntity = $entityFactory->get(BlogEntity::class);
    }
    
    public function generateRouteParams($url)
    {
        $postUrl = $this->matchProductUrl($url);
        $post    = $this->blogEntity->findOne(['url' => $postUrl]);

        if (empty($post)) {
            return $this->mockRouteParams;
        }

        return ['{$url}', ['{$url}' => $postUrl], ['{$url}' => $postUrl]];
    }

    private function matchProductUrl($url)
    {
        preg_match("/([^\/]+)/ui", $url, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }
}