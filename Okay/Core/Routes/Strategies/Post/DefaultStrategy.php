<?php


namespace Okay\Core\Routes\Strategies\Post;


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
        $prefix = $this->settings->get('post_routes_template__default');

        if (empty($prefix)) {
            $prefix = 'post';
        }

        return ['/'.$prefix.'/{$url}', [], []];
    }
}