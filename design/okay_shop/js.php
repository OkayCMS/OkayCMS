<?php
/**
 * Нужно вернуть массив объектов типа Okay\Core\TemplateConfig\Js
 * В конструктор объекта нужно передать один обязательный параметр - название файла
 * Если скрипт лежит не в стандартном месте (design/theme_name/js/)
 * нужно указать новое место, вызвав метод setDir() и передать путь к файл относительно корня сайта (DOCUMENT_ROOT)
 * Также можно вызвать метод setPosition() и указать head или footer (по умолчанию head)
 * @link https://github.com/OkayCMS/Okay3/blob/master/docs/js_css_files.md
 */

use Okay\Core\TemplateConfig\Js;

return [
    (new Js('jquery-3.4.1.min.js')),
    (new Js('swiper-bundle.min.js')),
    (new Js('nouislider.min.js'))->setPosition('footer'),
    (new Js('select2.min.js'))->setPosition('footer'),
    (new Js('okay.js'))->setPosition('footer'),
    (new Js('lazyload.min.js'))->setPosition('footer'),
    (new Js('jquery.fancybox.min.js'))->setPosition('footer'),
    (new Js('readmore.min.js'))->setPosition('footer'),
    (new Js('mobile_menu.js'))->setPosition('footer'),
    (new Js('sticky.min.js'))->setPosition('footer'),
    (new Js('jquery.autocomplete-min.js'))->setPosition('footer'),
    (new Js('jquery.validate.min.js'))->setPosition('footer'),
];
