<?php

use Okay\Core\Router;
use Okay\Core\TemplateConfig\Css;

$css = [];

if (Router::getCurrentRouteName() == 'cart') {
    $css[] = (new Css('np.css'))->setPosition('footer')->setIndividual(true);
}

return $css;
