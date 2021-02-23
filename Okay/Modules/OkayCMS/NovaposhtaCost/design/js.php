<?php

use Okay\Core\Router;
use Okay\Core\TemplateConfig\Js;


$js = [];

if (Router::getCurrentRouteName() == 'cart') {
    $js[] = (new Js('np.js'))->setPosition('footer')->setDefer(true)->setIndividual(true);
}

return $js;
