<?php


namespace Okay\Core\Routes;


use Okay\Core\Languages;
use Okay\Core\Request;
use Okay\Core\Routes\Strategies\AbstractRouteStrategy;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;

abstract class AbstractRoute
{
    /**
     * Данная константа переопределяется в наследниках и в ней указывается
     * название свойста Okay\Core\Setting::class, которое отвечает за определение
     * слеша в конце в конкретной группе роутов
     */
    const SLASH_END = '';

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var Languages
     */
    protected $languages;

    /**
     * @var AbstractRouteStrategy
     */
    protected $strategy;

    protected $isUsesSqlToGenerate;

    // Разрешено ли выполнять SQL запросы для формирования поля slug
    protected static $useSqlToGenerate = true;
    
    // Сочетания урла сущности и поля slug роута
    protected static $routeAliases;
    
    /**
     * Параметры которые были пойманы роутером при помощи регулярных выражения
     */
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;

        $serviceLocator = ServiceLocator::getInstance();
        $this->languages = $serviceLocator->getService(Languages::class);
        $this->settings = $serviceLocator->getService(Settings::class);
        $this->strategy = $this->getStrategy();
        $this->isUsesSqlToGenerate = $this->strategy->getIsUsesSqlToGenerate();
    }

    /**
     * Метод сообщает могут ли вообще использоваться SQL запросы для построения конкретного урла сущности 
     * (допустим доставаться категории или )
     * 
     * @return bool
     */
    public function getIsUsesSqlToGenerate()
    {
        return $this->isUsesSqlToGenerate;
    }

    /**
     * Метод возвращает разрешено ли выполнять SQL запросы для формирования поля slug (например доставать доп категории
     * или искать их в RouterCacheEntity)
     * 
     * @return bool
     */
    public static function getUseSqlToGenerate()
    {
        return self::$useSqlToGenerate;
    }

    /**
     * Метод сообщает генератору урлов что запрещено выполнять дополнительные запросы для формирования урла.
     * Может понадобиться во время работы с небуферизированными запросами
     */
    public static function setNotUseSqlToGenerate()
    {
        self::$useSqlToGenerate = false;
    }
    
    /**
     * Метод устанавливает связь между урлом сущности и его slug. Может быть необходимо когда весь slug генерируется
     * динамически (например все родительские категории) и нельзя выполнять запросы в базу (например работаем с 
     * небуферизированными запросами) можно установить связь урла и полностью поля slug (которое есть у RouterCacheEntity)
     * 
     * @param $url
     * @param $routeAlias
     */
    public static function setUrlSlugAlias($url, $routeAlias)
    {
        self::$routeAliases[$url] = $routeAlias;
    }

    public static function getUrlSlugAlias($url)
    {
        if (!empty(self::$routeAliases[$url])) {
            return self::$routeAliases[$url];
        }
        return false;
    }
    
    public function generateRouteParams()
    {
        $url = $this->prepareUrl(Request::getRequestUri());
        list($slug, $patterns, $defaults) = $this->strategy->generateRouteParams($url);
        return new RouteParams($slug, $patterns, $defaults);
    }

    public function generateSlugUrl($url)
    {
        return $this->strategy->generateSlugUrl($url);
    }

    private function prepareUrl($uri)
    {
        $uri = $this->removeLangPrefix($uri);

        return explode('?', $uri)[0];
    }

    private function removeLangPrefix($uri)
    {
        $langLink = $this->languages->getLangLink($this->languages->getLangId());
        return str_replace($langLink, '', $uri);
    }

    abstract public function hasSlashAtEnd();

    abstract protected function getStrategy();
}