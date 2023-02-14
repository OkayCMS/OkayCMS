<?php


namespace Okay\Core\Routes\Strategies\AllProducts;


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
        $prefix = 'all-products';

        return [
            '/'.$prefix.'/?{$filtersUrl}',
            ['{$filtersUrl}' => '(.*)'],
            []
        ];
    }
}