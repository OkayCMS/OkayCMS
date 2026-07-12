<?php

namespace Okay\Helpers;

use Okay\Core\Modules\Extender\ExtenderFacade;

/**
 * @phpstan-type PageRow object{visible: mixed}&\stdClass
 */
class PagesHelper
{
    /**
     * Метод проверяет доступность страницы для показа в контроллере
     * можно переопределить логику работы контроллера и отменить дальнейшие действия
     * для этого после реализации другой логики необходимо вернуть true из экстендера
     *
     * @param PageRow|false|null $page
     * @return false|null
     */
    public function setPage($page, $url)
    {
        if (empty($page) || (!$page->visible && empty($_SESSION['admin'])) || $url == '404') {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}
