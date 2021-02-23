<?php


namespace Okay\Core\Routes\Strategies;


abstract class AbstractRouteStrategy
{
    
    protected $isUsesSqlToGenerate = false;

    /**
     * Метод генерирует параметры, нужные роутеру для рендеринга страницы
     * 
     * @param $url
     * @return mixed
     */
    abstract public function generateRouteParams($url);

    /**
     * Метод генерирует и возвращает урл на основе slug роута (шаблона роута)
     * 
     * @param $url
     * @return null
     */
    public function generateSlugUrl($url)
    {
        return $url;
    }
    
    public function getIsUsesSqlToGenerate()
    {
        return $this->isUsesSqlToGenerate;
    }
}