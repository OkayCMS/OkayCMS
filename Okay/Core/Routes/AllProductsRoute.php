<?php


namespace Okay\Core\Routes;


use Okay\Core\Routes\Strategies\AllProducts\DefaultStrategy;

class AllProductsRoute extends AbstractRoute
{
    const SLASH_END = 'all_products_routes_template_slash_end';

    public function hasSlashAtEnd()
    {
        return intval($this->settings->get(static::SLASH_END)) === 1;
    }

    protected function getStrategy()
    {
        return new DefaultStrategy();
    }
}