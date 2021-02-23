<?php


namespace Okay\Core;

use Okay\Core\Entity\Entity;
use Bramus\Router\Router as BRouter;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Modules;
use Okay\Core\Routes\RouteFactory;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\RouterCacheEntity;
use Okay\Entities\PagesEntity;

class Router {

    const DEFAULT_CONTROLLER_NAMESPACE = '\\Okay\\Controllers\\';

    private static $currentRouteName;
    private $routeParams;
    private $routeRequiredParams;
    
    private static $routes;
    private static $modulesRoutes;

    /** @var BRouter */
    private $router;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /** @var EntityFactory */
    private static $entityFactory;

    /** @var ServiceLocator */
    private $serviceLocator;

    /** @var Modules */
    private $modules;

    /** @var Languages */
    private static $languages;

    /** @var Languages */
    private static $routeFactory;

    public function __construct(
        BRouter $router,
        Request $request,
        Response $response,
        EntityFactory $entityFactory,
        Languages $languages,
        RouteFactory $routeFactory,
        Modules $modules
    ) {
        
        // SL будем использовать только для получения сервисов, которые запросили для контроллера
        $this->serviceLocator = ServiceLocator::getInstance();
        
        $this->router        = $router;
        $this->request       = $request;
        $this->response      = $response;
        self::$entityFactory = $entityFactory;
        $this->modules       = $modules;
        self::$routeFactory  = $routeFactory;
        self::$languages     = $languages;
    }

    public static function getFrontRoutes()
    {
        self::initializeRoutes();
        $result = [];
        foreach (self::$routes as $name=>$route) {
            if (isset($route['to_front']) && $route['to_front'] === true) {
                $result[$name] = $route;
            }
        }
        
        return $result;
    }
    
    public static function getRouteByName($name)
    {
        self::initializeRoutes();
        return isset(self::$routes[$name]) ? self::$routes[$name] : false;
    }

    private function getFullControllerClassName($controllerName)
    {
        if ($this->classNameHasNoNamespace($controllerName)) {
            return self::DEFAULT_CONTROLLER_NAMESPACE.$controllerName;
        }

        return $controllerName;
    }

    private function methodExists($className, $methodName)
    {
        $allClassMethods = get_class_methods($className);
        return in_array($methodName, $allClassMethods);
    }

    /**
     * Запуск роутера. Здесь происходит регистрация всех роутов и последующее определение текущего роута.
     * Текущий роут определяется по полю slug из роута
     * @throws \Exception
     */
    public function run()
    {
        self::initializeRoutes();
        $router = $this->router;
        $routes = self::$routes;
        $request = $this->request;
        
        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = self::$entityFactory->get(LanguagesEntity::class);
        
        $language = $languagesEntity->get(self::$languages->getLangId());
        
        $baseRoute = '';
        $label = self::$languages->getLangLink($language->id);

        if (!empty(trim($label, '/'))) {
            $baseRoute = '/' . trim($label, '/');
        }

        foreach ($routes as $routeName => $route) {
            if (empty($route['params']['controller']) || empty($route['params']['method'])) {
                throw new \Exception('Route "'.$routeName.'" must contain two arguments named "controller" and "method" in "params" block');
            }

            $controllerClassName = $this->getFullControllerClassName($route['params']['controller']);
            if (!class_exists($controllerClassName)) {
                throw new \Exception('Class "'.$controllerClassName.'" uses in route "'.$routeName.'" is not exists');
            }

            if (!$this->methodExists($controllerClassName, $route['params']['method'])) {
                throw new \Exception('Method "'.$route['params']['method'].'" of "'.$controllerClassName.'" class uses in route "'.$routeName.'" is not exists');
            }

            if (!empty($route['mock'])) {
                continue;
            }
            
            $pattern = $baseRoute . $this->getPattern($route, $routeName);
            
            $router->all($pattern, function(...$params) use ($router, $route, $request, $language, $baseRoute, $routeName) {

                $flexibleRoute = self::$routeFactory->create($routeName, $params);
                if ($flexibleRoute) {
                    $currentUri           = Request::getCurrentQueryPath();
                    $lastSymbolCurrentUrl = mb_substr($currentUri, -1, 1);

                    if ($flexibleRoute->hasSlashAtEnd() && $lastSymbolCurrentUrl !== "/") {
                        $this->response->redirectTo($currentUri.'/', 301);
                        return;
                    }

                    if (! $flexibleRoute->hasSlashAtEnd() && $lastSymbolCurrentUrl === "/") {
                        $this->response->redirectTo(mb_substr($currentUri, 0, -1), 301);
                        return;
                    }
                }

                self::$currentRouteName = $routeName;
                $request->setBasePath($router->getBasePath());

                $request->setPageUrl($this->getCurrentUri(
                    $router->getCurrentUri(),
                    $baseRoute
                ));
                
                $routeVars = [];
                $controllerName = $route['params']['controller'];

                if ($this->classNameHasNoNamespace($controllerName)) {
                    $controllerName = self::DEFAULT_CONTROLLER_NAMESPACE . $controllerName;
                }
                $method = $route['params']['method'];

                $this->modules->registerSmartyPlugins();
                $this->modules->indexingNotInstalledModules();

                // Если язык выключен, отдадим 404
                if (!$language->enabled && empty($_SESSION['admin'])) {
                    $controllerName = self::DEFAULT_CONTROLLER_NAMESPACE . 'ErrorController';
                    $method = 'pageNotFound';
                }

                /** @var PagesEntity $pagesEntity */
                $pagesEntity = self::$entityFactory->get(PagesEntity::class);
                $page = $pagesEntity->get((string) $this->request->getPageUrl());
                if (!empty($page) && empty($page->visible)) {
                    $controllerName = self::DEFAULT_CONTROLLER_NAMESPACE . 'ErrorController';
                    $method = 'pageNotFound';
                }
                
                $defaults = isset($route['defaults']) ? $route['defaults'] : [];

                preg_match_all('~{\$(.+?)}~', $route['slug'], $matches);
                $routeVars = array_merge($routeVars, $matches[1]);

                $settings = $this->serviceLocator->getService(Settings::class);
                if ((!isset($route['always_active']) || $route['always_active'] !== true) && $settings->get('site_work') === 'off' && empty($_SESSION['admin'])) {
                    $controllerName = self::DEFAULT_CONTROLLER_NAMESPACE . 'ErrorController';
                    $method = 'siteDisabled';
                }
                
                include_once 'Okay/Core/SmartyPlugins/SmartyPlugins.php';

                // Если контроллер вернул false, кидаем 404
                if ($this->createControllerInstance($controllerName, $method, $params, $routeVars, $defaults) === false) {
                    $this->createControllerInstance(self::DEFAULT_CONTROLLER_NAMESPACE . 'ErrorController', 'pageNotFound', $params, $routeVars, $defaults);
                }
            });
        }

        $response = $this->response;
        
        $router->run(function() use ($response) {
            $response->sendContent();
        });
    }

    /**
     * Метод определяет текущий язык и устанавливает его в класс Languages
     */
    public function resolveCurrentLanguage()
    {
        $languages = self::$languages->getAllLanguages();
        $request = $this->request;

        $languages = array_reverse($languages);
        $router = clone $this->router;
        foreach ($languages as $language) {
            $label = self::$languages->getLangLink($language->id);
            if (!empty(trim($label, '/'))) {
                $pattern = '/' . trim($label, '/') . '(\/.*)?';
            } else {
                $pattern = '/.*';
            }

            $router->all($pattern, function() use ($language, $request) {
                self::$languages->setLangId((int)$language->id);
            });
        }
        $router->run();
    }

    private function classNameHasNoNamespace($className)
    {
        return strpos($className, '\\') === false;
    }

    private function createControllerInstance($controllerName, $methodName, $params = [], $routeVars = [], $defaults = [])
    {
        
        $controller = new $controllerName();

        $requiredParametersNames = [];
        $reflectionMethod = new \ReflectionMethod($controller, $methodName);
        foreach ($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable() === false) {
                $requiredParametersNames[] = $parameter->name;
            }
        }

        foreach ($this->getMethodParams($controller, $methodName, $params, $routeVars, $defaults, true) as $name=>$paramValue) {
            if (in_array($name, $requiredParametersNames)) {
                $this->routeRequiredParams[$name] = $paramValue;
            }
            $this->routeParams[$name] = $paramValue;
        }

        // Передаем контроллеру, все, что запросили
        if ($this->methodExists($controller, 'onInit')) {
            call_user_func_array([$controller, 'onInit'], $this->getMethodParams($controller, 'onInit', $params, $routeVars, $defaults));
        }
        // На 404 не вызываем afterController
        if (($controllerResult = call_user_func_array([$controller, $methodName], $this->getMethodParams($controller, $methodName, $params, $routeVars, $defaults))) !== false){
            if ($this->methodExists($controller, 'afterController')) {
                call_user_func_array([$controller, 'afterController'], $this->getMethodParams($controller, 'afterController', $params, $routeVars, $defaults));
            }
        }
        return $controllerResult;
    }
    
    /**
     * @return array
     * Метод возвращает все параметры, для которых не задан type hint (текстовые)
     * в виде ассоциативного массива, которые указаны в поле slug роута
     */
    public function getCurrentRouteParams()
    {
        return $this->routeParams;
    }
    
    /**
     * @return array
     * Метод возвращает все обязательные параметры, для которых не задан type hint (текстовые)
     * в виде ассоциативного массива, которые указаны в поле slug роута
     */
    public function getCurrentRouteRequiredParams()
    {
        return $this->routeRequiredParams;
    }

    /**
     * Метод добавляет в кеш роутов те, которых еще там нет. В кеш попадают урлы, для формирования 
     * которых нужно выполнить дополнительные действия (для товаров достають категоии и т.д.)
     * 
     * @throws \Exception
     */
    public static function generateRouterCache()
    {
        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = self::$entityFactory->get(RouterCacheEntity::class);

        // Если в генерации могут использоваться sql запросы, сгенерируем кеш для таких страниц
        
        $categoryRoute = self::$routeFactory->create('category');
        if ($categoryRoute->getIsUsesSqlToGenerate()) {
            $urls = $routerCacheEntity->getCategoriesUrlsWithoutCache();
            foreach ($urls as $url) {
                $categoryRoute->generateSlugUrl($url);
            }
        }

        $productRoute = self::$routeFactory->create('product');
        if ($productRoute->getIsUsesSqlToGenerate()) {
            $urls = $routerCacheEntity->getProductsUrlsWithoutCache();
            foreach ($urls as $url) {
                $productRoute->generateSlugUrl($url);
            }
        }

        $blogCategoryRoute = self::$routeFactory->create('blog_category');
        if ($blogCategoryRoute->getIsUsesSqlToGenerate()) {
            $urls = $routerCacheEntity->getBlogCategoriesUrlsWithoutCache();
            foreach ($urls as $url) {
                $blogCategoryRoute->generateSlugUrl($url);
            }
        }

        $postRoute = self::$routeFactory->create('post');
        if ($postRoute->getIsUsesSqlToGenerate()) {
            $urls = $routerCacheEntity->getBlogUrlsWithoutCache();
            foreach ($urls as $url) {
                $postRoute->generateSlugUrl($url);
            }
        }
    }
    
    public static function generateUrl($routeName, $params = [], $isAbsolute = false, $langId = null)
    {
        $route = self::$routeFactory->create($routeName);
        if ($route !== false && !empty($params['url'])) {
            $params['url'] = $route->generateSlugUrl($params['url']);
        }

        if (empty($routeName)) {
            throw new \Exception('Empty param "route"');
        }

        if (!$routeInfo = self::getRouteByName($routeName)) {
            throw new \Exception("Route \"{$routeName}\" not found");
        }

        unset($params['route']);

        // Перебираем переданные параметры, чтобы подставить их как элементы роута
        $urlData = [];
        if (!empty($params)) {
            foreach ($params as $var=>$param) {
                $urlData['{$' . $var . '}'] = $param;
            }
        }

        $slug = $routeInfo['slug'];
        $slug = str_replace('/?', '/', $slug);
        
        $result = trim(strtr($slug, $urlData), '/');

        // Если это не внешний урл, добавим языковой префикс
        if (!preg_match('~^https?://~', $result)) {
            $result = self::$languages->getLangLink($langId) . $result;
        }

        $result = preg_replace('~{\$[^$]*}~', '', $result);
        $result = trim($result, '/');
        
        if ($isAbsolute === true) {
            $result = Request::getRootUrl() . '/' . $result;
        } else {
            // Все урлы строим абсолютными, относительно домена, даже если сайт в подпапке, добавим и подпапку
            $result = Request::getSubDir() . '/' . $result;
        }

        $result = trim(strip_tags(htmlspecialchars($result)));
        
        // TODO здесь есть скрытая связь с FilterHelper. Это может привести к багам, подумать над тем, чтобы решить это
        if (is_object($route) && method_exists($route, 'hasSlashAtEnd') && $route->hasSlashAtEnd()) {
            return ExtenderFacade::execute(__METHOD__, rtrim($result, '/').'/', func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
    /**
     * @return string
     *
     * Метод возвращает имя текущего роута
     */
    public static function getCurrentRouteName()
    {
        return self::$currentRouteName;
    }

    /**
     * Метод на основании поля slug роута генерирует регулярное выражение
     * @param array $route
     * @param string $routeName
     * @return string pattern
     */
    public function getPattern($route, $routeName)
    {
        $pattern = !empty($route['patterns']) ? strtr($route['slug'], $route['patterns']) : $route['slug'];
        $pattern = trim(preg_replace('~{\$.+?}~', '([^/]+)', $pattern), '/');
        return ExtenderFacade::execute(__METHOD__, !empty($pattern) ? '/' . $pattern : $pattern, func_get_args());
    }

    /**
     * Добавляет новые маршруты в реестр класса роутера
     * @param $routes
     * @throws \Exception Route name already uses
     * @return void
     */
    public static function bindRoutes(array $routes)
    {
        foreach ($routes as $routeName => $route) {
            self::$modulesRoutes[$routeName] = $route;
        }
    }

    /**
     * @param $controller
     * @param $methodName
     * @param array $routeParams
     * @param array $routeVars
     * @param array $defaults
     * @param bool $stringOnly - возвращать все параметры или только текстовые
     * @return array ассоциативный массив, где ключ - название параметра, 
     * значение - экземпляк класса, который указали как Type hint или строка, которая соответствует части урла
     * @throws \ReflectionException
     */
    private function getMethodParams($controller, $methodName, $routeParams = [], $routeVars = [], $defaults = [], $stringOnly = false)
    {
        $methodParams = [];
        $allParams = [];
        
        // Перебираем переменные роута, чтобы заполнить их дефолтными значениями
        if (!empty($routeVars)) {
            foreach ($routeVars as $key => $routeVar) {
                $param = isset($routeParams[$key]) ? $routeParams[$key] : null;
                $param = strip_tags(htmlspecialchars($param));
                
                $allParams[$routeVar] = (empty($param) && !empty($defaults['{$' . $routeVar . '}']) ? $defaults['{$' . $routeVar . '}'] : $param);
            }
        }
        
        // Проходимся рефлексией по параметрам метода, определяем их тип, и пытаемся через DI передать нужный объект
        // Если тип не указан, тогда связываем название переменной в поле slug роута, с названием аргумента метода
        $reflectionMethod = new \ReflectionMethod($controller, $methodName);
        foreach ($reflectionMethod->getParameters() as $parameter) {
            
            if ($parameter->getClass() !== null) { // если для аргумента указан type hint, передадим экземляр соответствующего класса
                if ($stringOnly === false) {
                    // Определяем это Entity или сервис из DI
                    if (is_subclass_of($parameter->getClass()->name, Entity::class)) {
                        $methodParams[$parameter->getClass()->name] = self::$entityFactory->get($parameter->getClass()->name);
                    } else {
                        $methodParams[$parameter->getClass()->name] = $this->serviceLocator->getService($parameter->getClass()->name);
                    }
                }
            } elseif (!empty($allParams[$parameter->name]) || array_key_exists($parameter->name, $allParams)) { // если тип не указан, передаем строковую переменную как значение из поля slug (то, что попало под регулярку)
                $methodParams[$parameter->name] = $allParams[$parameter->name];
            } elseif (!empty($defaults['{$' . $parameter->name . '}'])) { // на крайний случай, может в поле defaults роута указано значение этой переменной
                $methodParams[$parameter->name] = $defaults['{$' . $parameter->name . '}'];
            } elseif ($parameter->isDefaultValueAvailable() === false) { // Если не нашли значения аргументу, и он не имеет значения по умолчанию в методе контроллера, ошибка
                $controllerName = $reflectionMethod->getDeclaringClass()->name;
                throw new \Exception("Missing argument \"\${$parameter->name}\" in \"{$controllerName}->{$methodName}()\"");
            }
        }

        return $methodParams;
    }

    private function getCurrentUri($currentUri, $baseUri)
    {
        return preg_replace('~^('.$baseUri.'/?)(.*)$~', '$2', $currentUri);
    }

    /**
     * Метод инициализирует системные роуты, вызывать можно сколько угодно, отработает только раз
     */
    private static function initializeRoutes()
    {
        if (($routes = require_once 'Okay/Core/config/routes.php') && is_array($routes)) {
            
            if (!empty(self::$modulesRoutes)) {
                $modulesRoutes = [];
                foreach (self::$modulesRoutes as $routeName => $route) {
                    if (array_key_exists($routeName, $routes)) {
                        if ($route['overwrite'] == true) {
                            $modulesRoutes[$routeName] = $route;
                        } else {
                            throw new \Exception('Route name "' . $routeName . '" already uses');
                        }
                    } else {
                        $modulesRoutes[$routeName] = $route;
                    }
                }
                $routes = array_merge($modulesRoutes, $routes);
            }
            
            self::$routes = $routes;
        }
    }
    
}
