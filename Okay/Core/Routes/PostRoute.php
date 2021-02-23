<?php


namespace Okay\Core\Routes;


use Okay\Core\Routes\Strategies\Post\DefaultStrategy;
use Okay\Core\Routes\Strategies\Post\NoPrefixAndCategoryStrategy;
use Okay\Core\Routes\Strategies\Post\NoPrefixStrategy;
use Okay\Core\Routes\Strategies\Post\PrefixAndPathStrategy;
use Okay\Core\Routes\Strategies\Post\NoPrefixAndPathStrategy;

class PostRoute extends AbstractRoute
{
    const POST_ROUTE_TEMPLATE      = 'post_routes_template';
    const TYPE_NO_PREFIX              = 'no_prefix';
    const TYPE_PREFIX_AND_PATH        = 'prefix_and_path';
    const TYPE_NO_PREFIX_AND_PATH     = 'no_prefix_and_path';
    const TYPE_NO_PREFIX_AND_CATEGORY = 'no_prefix_and_category';
    const SLASH_END                   = 'post_routes_template_slash_end';

    protected static $useSqlToGenerate;
    protected static $routeAliases;
    
    public function hasSlashAtEnd()
    {
        return intval($this->settings->get(static::SLASH_END)) === 1;
    }

    protected function getStrategy()
    {
        if (static::TYPE_NO_PREFIX === $this->settings->get(static::POST_ROUTE_TEMPLATE)) {
            return new NoPrefixStrategy();
        }

        if (static::TYPE_NO_PREFIX_AND_PATH === $this->settings->get(static::POST_ROUTE_TEMPLATE)) {
            return new NoPrefixAndPathStrategy();
        }

        if (static::TYPE_NO_PREFIX_AND_CATEGORY === $this->settings->get(static::POST_ROUTE_TEMPLATE)) {
            return new NoPrefixAndCategoryStrategy();
        }

        if (static::TYPE_PREFIX_AND_PATH === $this->settings->get(static::POST_ROUTE_TEMPLATE)) {
            return new PrefixAndPathStrategy();
        }

        return new DefaultStrategy();
    }
}