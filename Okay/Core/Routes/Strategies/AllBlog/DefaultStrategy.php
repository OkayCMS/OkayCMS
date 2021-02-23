<?php


namespace Okay\Core\Routes\Strategies\AllBlog;


use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\Settings;
use Okay\Core\ServiceLocator;

class DefaultStrategy extends AbstractRouteStrategy
{
    private $settings;

    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $this->settings = $serviceLocator->getService(Settings::class);
    }

    public function generateRouteParams($url)
    {
        $prefix = $this->settings->get('all_blog_routes_template__default');

        if (empty($prefix)) {
            $prefix = 'all-posts';
        }

        return ['/'.$prefix, [], []];
    }
}