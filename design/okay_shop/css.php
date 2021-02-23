<?php
/**
 * Нужно вернуть массив объектов типа Okay\Core\TemplateConfig\Css
 * В конструктор объекта нужно передать один обязательный параметр - название файла
 * Если скрипт лежит не в стандартном месте (design/theme_name/css/)
 * нужно указать новое место, вызвав метод setDir() и передать путь к файл относительно корня сайта (DOCUMENT_ROOT)
 * Также можно вызвать метод setPosition() и указать head или footer (по умолчанию head)
 * @link https://github.com/OkayCMS/Okay3/blob/master/docs/js_css_files.md
 */

use Okay\Core\TemplateConfig\Css;

return [
    (new Css('font-awesome.min.css')),
    (new Css('grid.css')),
    (new Css('okay.css')),
    (new Css('theme.css')),
    (new Css('select2.min.css')),
    (new Css('jquery.fancybox.min.css')),
    (new Css('media.css')),
    (new Css('mobile_menu.css'))
];

