<?php

namespace Okay\Modules\OkayCMS\Feeds\Core;

use Okay\Core\Modules\Extender\ExtenderFacade;

trait InheritedExtenderTrait
{
    /**
     * Расширяет метод в двух классах: в котором он задекларирован и в котором он был вызван(если метод не был переопределён).
     * НЕ расширяет метод для всех промежуточных классов.
     */
    protected function inheritedExtender($method, $output = null, array $input = [])
    {
        $output = ExtenderFacade::execute([self::class, $method], $output, func_get_args());

        $declaringClass = (new \ReflectionMethod(static::class, $method))->getDeclaringClass()->name;
        if ($declaringClass === self::class) {
            $output = ExtenderFacade::execute([static::class, $method], $output, func_get_args());
        }

        return $output;
    }
}