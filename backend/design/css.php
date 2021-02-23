<?php
/**
 * Нужно вернуть массив объектов типа Okay\Core\TemplateConfig\Css
 * В конструктор объекта нужно передать один обязательный параметр - название файла
 * Если скрипт лежит не в стандартном месте (design/theme_name/css/)
 * нужно указать новое место, вызвав метод setDir() и передать путь к файл относительно корня сайта (DOCUMENT_ROOT)
 * Также можно вызвать метод setPosition() и указать head или footer (по умолчанию head)
 * todo ссылка на документацию
 */

use Okay\Core\TemplateConfig\Css;

return [
    (new Css('jquery-ui.min.css'))->setDir('backend/design/js/jquery'),
    (new Css('jquery.fancybox.min.css')), 
    (new Css('grid.css')),
    (new Css('reboot.css')),
    (new Css('font-awesome.min.css')),
    (new Css('toastr.css')),
    (new Css('simple-hint.css')),
    (new Css('bootstrap-select.css')),
    (new Css('jquery.scrollbar.css')),
    (new Css('bootstrap_theme.css')),
    (new Css('okay.css')),
    (new Css('media.css')),
    (new Css('introjs.css'))->setDir('backend/design/js/intro_js'),
];

