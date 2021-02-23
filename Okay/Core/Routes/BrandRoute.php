<?php


namespace Okay\Core\Routes;


use Okay\Core\Routes\Strategies\Brand\NoPrefixStrategy;
use Okay\Core\Routes\Strategies\Brand\DefaultStrategy;

class BrandRoute extends AbstractRoute
{
    const BRAND_ROUTE_TEMPLATE = 'brand_routes_template';
    const TYPE_NO_PREFIX       = 'no_prefix';
    const SLASH_END            = 'brand_routes_template_slash_end';

    public function hasSlashAtEnd()
    {
        return intval($this->settings->get(static::SLASH_END)) === 1;
    }

    protected function getStrategy()
    {
        if (static::TYPE_NO_PREFIX === $this->settings->get(static::BRAND_ROUTE_TEMPLATE)) {
            return new NoPrefixStrategy();
        }

        return new DefaultStrategy();
    }
}