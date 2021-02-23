<?php
/**
 * Нужно вернуть массив объектов типа Okay\Core\TemplateConfig\Js
 * В конструктор объекта нужно передать один обязательный параметр - название файла
 * Если скрипт лежит не в стандартном месте (design/theme_name/js/)
 * нужно указать новое место, вызвав метод setDir() и передать путь к файл относительно корня сайта (DOCUMENT_ROOT)
 * Также можно вызвать метод setPosition() и указать head или footer (по умолчанию head)
 * todo ссылка на документацию
 */

use Okay\Core\TemplateConfig\Js;

return [
    (new Js('jquery/jquery.js')),
    (new Js('jquery.scrollbar.min.js')),
    (new Js('bootstrap.min.js')),
    (new Js('bootstrap-select.js')),
    (new Js('jquery/jquery-ui.min.js')),
    (new Js('jquery.dd.min.js')),
    (new Js('fancybox/jquery.fancybox.min.js')),
    (new Js('intro_js/intro.js')),
    (new Js('intro_js/intro_okay.js')),
    (new Js('toastr.min.js')),
    (new Js('Sortable.js')),
];
