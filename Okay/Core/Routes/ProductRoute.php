<?php


namespace Okay\Core\Routes;


use Okay\Core\Routes\Strategies\Product\DefaultStrategy;
use Okay\Core\Routes\Strategies\Product\NoPrefixAndCategoryStrategy;
use Okay\Core\Routes\Strategies\Product\NoPrefixStrategy;
use Okay\Core\Routes\Strategies\Product\PrefixAndPathStrategy;
use Okay\Core\Routes\Strategies\Product\NoPrefixAndPathStrategy;

class ProductRoute extends AbstractRoute
{
    const PRODUCT_ROUTE_TEMPLATE      = 'product_routes_template';
    const TYPE_NO_PREFIX              = 'no_prefix';
    const TYPE_PREFIX_AND_PATH        = 'prefix_and_path';
    const TYPE_NO_PREFIX_AND_PATH     = 'no_prefix_and_path';
    const TYPE_NO_PREFIX_AND_CATEGORY = 'no_prefix_and_category';
    const SLASH_END                   = 'product_routes_template_slash_end';

    protected static $useSqlToGenerate;
    protected static $routeAliases;
    
    public function hasSlashAtEnd()
    {
        return intval($this->settings->get(static::SLASH_END)) === 1;
    }

    protected function getStrategy()
    {
        if (static::TYPE_NO_PREFIX === $this->settings->get(static::PRODUCT_ROUTE_TEMPLATE)) {
            return new NoPrefixStrategy();
        }

        if (static::TYPE_NO_PREFIX_AND_PATH === $this->settings->get(static::PRODUCT_ROUTE_TEMPLATE)) {
            return new NoPrefixAndPathStrategy();
        }

        if (static::TYPE_NO_PREFIX_AND_CATEGORY === $this->settings->get(static::PRODUCT_ROUTE_TEMPLATE)) {
            return new NoPrefixAndCategoryStrategy();
        }

        if (static::TYPE_PREFIX_AND_PATH === $this->settings->get(static::PRODUCT_ROUTE_TEMPLATE)) {
            return new PrefixAndPathStrategy();
        }

        return new DefaultStrategy();
    }
}