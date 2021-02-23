<?php


namespace Okay\Core\TemplateConfig;


class Js extends Common
{
    protected $defer = false;

    /**
     * Установка скрипту флага defer.
     * defer будет добавлен в случае individual = true
     * @param bool $defer
     * @return $this
     */
    public function setDefer($defer)
    {
        $this->defer = $defer;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getDefer()
    {
        return $this->defer;
    }

}