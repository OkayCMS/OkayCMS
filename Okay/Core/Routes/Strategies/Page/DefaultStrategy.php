<?php


namespace Okay\Core\Routes\Strategies\Page;


use Okay\Core\Routes\Strategies\AbstractRouteStrategy;

class DefaultStrategy extends AbstractRouteStrategy
{
    public function generateRouteParams($url)
    {
        return ['{$url}', ['{$url}' => '(.*)'], []];
    }
}