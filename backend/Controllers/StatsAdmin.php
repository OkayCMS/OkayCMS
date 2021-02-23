<?php


namespace Okay\Admin\Controllers;


class StatsAdmin extends IndexAdmin
{

    /*Отображение модуля статистики продаж*/
    public function fetch()
    {
        $this->response->setContent($this->design->fetch('stats.tpl'));
    }
    
}
