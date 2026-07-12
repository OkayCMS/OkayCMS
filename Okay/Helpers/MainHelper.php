<?php

namespace Okay\Helpers;

use Okay\Core\Cart;
use Okay\Core\Comparison;
use Okay\Core\Config;
use Okay\Core\DebugBar\DebugBar;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\JsSocial;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Module;
use Okay\Core\Phone;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Core\Security\CheckoutToken;
use Okay\Core\Security\CustomerCsrfToken;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\UserReferer\UserReferer;
use Okay\Core\WishList;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\MenuEntity;
use Okay\Entities\MenuItemsEntity;
use Okay\Entities\PagesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\UserGroupsEntity;
use Okay\Entities\UsersEntity;
use Okay\Helpers\MetadataHelpers\CommonMetadataHelper;
use Okay\Helpers\MetadataHelpers\MetadataInterface;

/**
 * @phpstan-type CurrencyRow object{id: string|int}&\stdClass
 * @phpstan-type VisibleTreeItem object{parent_id: string|int|null, visible: bool|int, count_children_visible?: int}&\stdClass
 */
class MainHelper
{
    private static $initialized = false;
    private $allLanguages;
    private $allCurrencies;
    private $currentLanguage;
    private $currentCurrency;
    private $currentUser;
    private $currentUserGroup;
    private $currentPage;
    private $SL;

    public function __construct()
    {
        $this->SL = ServiceLocator::getInstance();
    }

    public function init()
    {

        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->SL->getService(EntityFactory::class);
        /** @var Request $request */
        $request = $this->SL->getService(Request::class);
        /** @var Languages $languages */
        $languages = $this->SL->getService(Languages::class);
        /** @var Response $response */
        $response = $this->SL->getService(Response::class);

        $languagesEntity = $entityFactory->get(LanguagesEntity::class);
        $langId = $languages->getLangId();
        $this->currentLanguage = $languagesEntity->get($langId);
        $this->allLanguages = $languagesEntity->find();

        /** @var PagesEntity $pagesEntity */
        $pagesEntity = $entityFactory->get(PagesEntity::class);
        $this->currentPage = $pagesEntity->get($request->getPageUrl());

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $entityFactory->get(CurrenciesEntity::class);
        // Все валюты
        $this->allCurrencies = $currenciesEntity->find(['enabled' => 1]);

        // Выбор текущей валюты
        if ($currencyId = $request->get('currency_id', 'integer')) {
            $_SESSION['currency_id'] = $currencyId;
            $response->redirectTo($request->url(['currency_id' => null]));
        }
        // Берем валюту из сессии
        if (isset($_SESSION['currency_id'])) {
            $this->currentCurrency = $currenciesEntity->get((int)$_SESSION['currency_id']);
        } else {
            $this->currentCurrency = reset($this->allCurrencies);
        }

        // Если валюта не найдена или массив валют пуст, берем первую доступную
        if (empty($this->currentCurrency) || !is_object($this->currentCurrency)) {
            $this->currentCurrency = reset($this->allCurrencies);
        }

        // Если все еще нет валюты, создаем дефолтную
        if (empty($this->currentCurrency) || !is_object($this->currentCurrency)) {
            // Fallback: создаем объект с дефолтными значениями
            $this->currentCurrency = (object)[
                'id' => 0,
                'code' => 'USD',
                'sign' => '$',
                'rate_from' => 1,
                'rate_to' => 1,
                'cents' => 2,
            ];
        } else {
            /** @var CurrencyRow $currentCurrency */
            $currentCurrency = $this->currentCurrency;
            $_SESSION['currency_id'] = $currentCurrency->id;
        }

        // Пользователь, если залогинен
        if (isset($_SESSION['user_id'])) {
            /** @var UsersEntity $usersEntity */
            $usersEntity = $entityFactory->get(UsersEntity::class);

            /** @var UserGroupsEntity $userGroupsEntity */
            $userGroupsEntity = $entityFactory->get(UserGroupsEntity::class);

            $user = $usersEntity->get((int)$_SESSION['user_id']);
            if (!empty($user)) {
                $this->currentUser = $user;
                $this->currentUserGroup = $userGroupsEntity->get($this->currentUser->group_id);
            } else {
                unset($_SESSION['user_id']);
            }
        }

        // Пытаемся определить откуда пришел пользователь, и запоминаем эту информацию
        if (!$referer = UserReferer::getUserReferer()) {
            /** @var UserReferer $userReferer */
            $userReferer = $this->SL->getService(UserReferer::class);
            $userReferer->parse();
        }
    }

    /**
     * Метод, который можно расширять модулями. Выполняется перед работой контроллера
     */
    public function commonBeforeControllerProcedure()
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод, который можно расширять модулями. Выполняется он после работы контроллера
     *
     * @var MetadataInterface|null $metadataHelper
     * @throws \Exception
     */
    public function commonAfterControllerProcedure($metadataHelper)
    {
        /** @var Design $design */
        $design = $this->SL->getService(Design::class);

        // Если в контроллере не передали хелпер метаданных, утановим стандартный
        if ($metadataHelper === null) {
            /** @var MetadataInterface $metadataHelper */
            $metadataHelper = $this->SL->getService(CommonMetadataHelper::class);
        }

        if ($design->getVar('h1') === null) {
            $design->assign('h1', $metadataHelper->getH1());
        }

        if ($design->getVar('meta_title') === null) {
            $design->assign('meta_title', $metadataHelper->getMetaTitle());
        }

        if ($design->getVar('meta_keywords') === null) {
            $design->assign('meta_keywords', $metadataHelper->getMetaKeywords());
        }

        if ($design->getVar('meta_description') === null) {
            $design->assign('meta_description', $metadataHelper->getMetaDescription());
        }

        if ($design->getVar('annotation') === null) {
            $design->assign('annotation', $metadataHelper->getAnnotation());
        }

        if ($design->getVar('description') === null) {
            $design->assign('description', $metadataHelper->getDescription());
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод передает в дизайн все переменные, которые могут там понадобиться
     *
     * @throws \Exception
     */
    public function setDesignDataProcedure()
    {
        if (self::$initialized === true) {
            return;
        }

        /** @var Design $design */
        $design = $this->SL->getService(Design::class);
        /** @var Request $request */
        $request = $this->SL->getService(Request::class);
        /** @var Settings $settings */
        $settings = $this->SL->getService(Settings::class);
        /** @var Router $router */
        $router = $this->SL->getService(Router::class);
        /** @var Phone $phone */
        $phone = $this->SL->getService(Phone::class);
        /** @var FilterHelper $filterHelper */
        $filterHelper = $this->SL->getService(FilterHelper::class);
        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->SL->getService(EntityFactory::class);
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        /** @var BlogCategoriesEntity $blogCategoriesEntity */
        $blogCategoriesEntity = $entityFactory->get(BlogCategoriesEntity::class);
        /** @var PagesEntity $pagesEntity */
        $pagesEntity = $entityFactory->get(PagesEntity::class);

        $pages = $pagesEntity->find(['visible' => 1]);
        $design->assign('pages', $pages);

        // Передаём в дизайн DebugBarRenderer
        $design->assign('debug_bar_renderer', DebugBar::getRenderer());

        // Передаем стили и скрипты в шаблон
        /** @var FrontTemplateConfig $frontTemplateConfig */
        $frontTemplateConfig = $this->SL->getService(FrontTemplateConfig::class);

        $frontTemplateConfig->compileFiles();
        $design->assign('ok_head', $frontTemplateConfig->head());
        $design->assign('ok_footer', $frontTemplateConfig->footer());

        // Передаем в дизайн название текущего роута
        $design->assign('route_name', $router->getCurrentRouteName());
        $design->assign('current_page', $request->get('page'));

        // Передаем переводы
        $design->assign('lang', $this->SL->getService(FrontTranslations::class));

        $design->assign('settings', $this->SL->getService(Settings::class));
        $design->assign('config', $this->SL->getService(Config::class));
        $design->assign('rootUrl', $request->getRootUrl());
        $customerCsrfToken = CustomerCsrfToken::get();
        $design->assign('customer_csrf_token', $customerCsrfToken);
        $design->assignJsVar('customer_csrf_token', $customerCsrfToken);
        $design->assign('checkout_token', CheckoutToken::get());

        $design->assign('is_mobile', $design->isMobile());
        $design->assign('is_tablet', $design->isTablet());

        $design->assign('language', $this->getCurrentLanguage());
        $design->assign('languages', $this->getAllLanguages());

        $design->assign('base', $request->getRootUrl());

        $design->assign('cart', $this->SL->getService(Cart::class)->get());
        $design->assign('wishlist', $this->SL->getService(WishList::class)->get());
        $design->assign('comparison', $this->SL->getService(Comparison::class)->get());

        $design->assign('page', $this->getCurrentPage());

        $design->assign('currencies', $this->getAllCurrencies());
        $currentCurrency = $this->getCurrentCurrency();
        $design->assign('currency', $currentCurrency);
        if ($currentCurrency && is_object($currentCurrency) && isset($currentCurrency->cents)) {
            $design->assignJsVar('currency_cents', $currentCurrency->cents);
        } else {
            $design->assignJsVar('currency_cents', 2);
        }

        $design->assign('user', $this->getCurrentUser());
        $design->assign('group', $this->getCurrentUserGroup());

        $design->assign('payment_methods', $this->getPaymentMethods());
        $design->assign('phone_example', $phone->getPhoneExample());

        $design->assign('keyword', $filterHelper->getKeyword());
        $design->assign('keyword', $filterHelper->getKeyword(), true);

        if (!empty($settings->get('site_social_links'))) {
            $socials = [];
            foreach ($settings->get('site_social_links') as $k => $socialUrl) {
                if (empty($socialUrl)) {
                    continue;
                }
                $social['domain'] = JsSocial::getSocialDomain($socialUrl);
                $social['url'] = $socialUrl;
                $socials[] = $social;
            }

            $design->assign('site_social', $socials);
        }

        // Категории товаров
        /** @var array<int|string, VisibleTreeItem> $allCategories */
        $allCategories = $categoriesEntity->find();
        $this->countVisible($categoriesEntity->getCategoriesTree(), $allCategories);
        $design->assign('categories', $categoriesEntity->getCategoriesTree());

        // Категории блога
        /** @var array<int|string, VisibleTreeItem> $allBlogCategories */
        $allBlogCategories = $blogCategoriesEntity->find();
        $this->countVisible($blogCategoriesEntity->getCategoriesTree(), $allBlogCategories);
        $design->assign('blog_categories', $blogCategoriesEntity->getCategoriesTree());

        $design->assign('share_networks', $this->SL->getService(JsSocial::class)->getShareNetworks());
        $design->assign('share_icons_theme', $settings->get('social_share_theme') ?: 'default');

        // Передаем счетчики
        $counters = [];
        if (!empty($settings->get('counters'))) {
            foreach ($settings->get('counters') as $c) {
                $counters[$c->position][] = $c;
            }
        }
        $design->assign('counters', $counters);

        // Передаем менюшки
        $menuEntity = $entityFactory->get(MenuEntity::class);
        $menuItemsEntity = $entityFactory->get(MenuItemsEntity::class);
        $menus = $menuEntity->find(['visible' => 1]);
        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $design->assign("menu", $menu);
                $all_menu_items = $menuItemsEntity->getMenuItems();
                $this->countVisible($menuItemsEntity->getMenuItemsTree((int)$menu->id), $all_menu_items, 'submenus');
                $design->assign("menu_items", $menuItemsEntity->getMenuItemsTree((int)$menu->id));
                $design->assign(MenuEntity::MENU_VAR_PREFIX . $menu->group_id, $design->fetch("menu.tpl"));
            }
        }

        // Передаем текущий контроллер
        if ($route = $router->getRouteByName($router->getCurrentRouteName())) {
            $design->assign('controller', $route['params']['controller']);
        }

        // Передаем все что нам пришло постом, обратно в дизайн, будем из него считывать.
        if (!empty($_POST)) {
            $requestData = [];
            foreach (array_keys($_POST) as $field) {
                $requestData[$field] = $request->post($field);
            }
            $design->assign('request_data', $requestData);
        }

        self::$initialized = true;

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод возвращает все языки сайта, с урлами
     *
     * @return list<object>
     * @throws \Exception
     */
    public function getAllLanguages()
    {
        foreach ($this->allLanguages as $l) {
            if ($url = $this->getLangUrl($l->id)) {
                $l->url = $url;
            }
        }
        return ExtenderFacade::execute(__METHOD__, $this->allLanguages, func_get_args());
    }

    /**
     * Метод возвращает текущий язык сайта с урлом
     *
     * @return object|null
     * @throws \Exception
     */
    public function getCurrentLanguage()
    {
        if ($url = $this->getLangUrl($this->currentLanguage->id)) {
            $this->currentLanguage->url = $url;
        }
        return ExtenderFacade::execute(__METHOD__, $this->currentLanguage, func_get_args());
    }

    /**
     * Метод возвращает все активные валюты сайта
     *
     * @return list<object>
     */
    public function getAllCurrencies()
    {
        return ExtenderFacade::execute(__METHOD__, $this->allCurrencies, func_get_args());
    }

    /**
     * Метод возвращает текущую валюту пользователя
     *
     * @return mixed|void|null
     */
    public function getCurrentCurrency()
    {
        return ExtenderFacade::execute(__METHOD__, $this->currentCurrency, func_get_args());
    }

    /**
     * Метод возвращает текущую страницу сайта
     *
     * @return (object{url: string, last_modify: mixed}&\stdClass)|null
     */
    public function getCurrentPage()
    {
        return ExtenderFacade::execute(__METHOD__, $this->currentPage, func_get_args());
    }

    /**
     * Метод возвращает текущего пользователя, если он залогинен
     *
     * @return (object{id: int|string, name?: string, preferred_payment_method_id?: int|string|null, preferred_delivery_id?: int|string|null}&\stdClass)|null
     */
    public function getCurrentUser()
    {
        return ExtenderFacade::execute(__METHOD__, $this->currentUser, func_get_args());
    }

    /**
     * Метод возвращает группу текущего пользователя, если он залогинен и принадлежит какой-то группе
     *
     * @return object|null
     */
    public function getCurrentUserGroup()
    {
        return ExtenderFacade::execute(__METHOD__, $this->currentUserGroup, func_get_args());
    }

    /**
     * Метод возвращает урл текущей страницы для другого языка, указанного как $langId
     *
     * @param int $langId ID языка для которого генерируем урл
     * @return string
     * @throws \Exception
     */
    private function getLangUrl(int $langId): string
    {
        /** @var Router $router */
        $router = $this->SL->getService(Router::class);
        if (empty($router->getCurrentRouteName())) {
            return false;
        }
        $routeParams = $router->getCurrentRouteRequiredParams();
        $route = $router->generateUrl($router->getCurrentRouteName(), $routeParams, true, $langId);
        return ExtenderFacade::execute(__METHOD__, $route, func_get_args());
    }

    /**
     * Метод подготавливает данные для отображения их в динамических js файлах
     * Это результаты компиляции файлов scripts.tpl и common_js.tpl
     */
    public function activateDynamicJs()
    {

        /** @var Router $router */
        $router = $this->SL->getService(Router::class);

        // Если пришли не за скриптом, очищаем все переменные для динамического JS
        if (($routeName = $router->getCurrentRouteName()) != 'dynamic_js' && $routeName != 'common_js' && $routeName != 'resize') {
            unset($_SESSION['dynamic_js']);
            $route = $router->getRouteByName($routeName);
            $_SESSION['dynamic_js']['controller'] = $route['params']['controller'];
        }

        if (($routeName = $router->getCurrentRouteName()) != 'common_js') {
            $route = $router->getRouteByName($routeName);
            $_SESSION['common_js']['controller'] = $route['params']['controller'];
        }
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод проверяет есть ли запрос prg_seo_hide (это поле постом)
     * Если есть, метод редиректит нас на указанный урл
     *
     * PRG (Post/Redirect/Get) используется в данном случае для закрытия некоторых ссылок на сайте.
     * Это достигается тем, что на сайте вместо ссылки на нужную страницу можно поставить форму
     * с типом POST и одним инпутом name="prg_seo_hide" где в значении можно указать АБСОЛЮТНЫЙ путь
     * куда нужно перенаправить пользователя
     *
     * @throws \Exception
     */
    public function activatePRG()
    {

        /** @var Request $request */
        $request = $this->SL->getService(Request::class);

        if ($prgSeoHide = $request->post("prg_seo_hide")) {
            if (!$this->isSafePrgRedirect((string)$prgSeoHide)) {
                return ExtenderFacade::execute(__METHOD__, null, func_get_args());
            }
            Response::redirectTo($prgSeoHide);
            exit;
        }
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function isSafePrgRedirect(string $url): bool
    {
        if ($url === '' || str_contains($url, "\r") || str_contains($url, "\n")) {
            return false;
        }

        $decoded = rawurldecode($url);
        if (str_starts_with($decoded, '//')) {
            return false;
        }

        if (str_contains($decoded, '\\')) {
            return false;
        }

        if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $decoded)) {
            return $this->isSameOriginPrgRedirect($decoded);
        }

        return str_starts_with($decoded, '/')
            || !str_starts_with($decoded, '.');
    }

    private function isSameOriginPrgRedirect(string $url): bool
    {
        $urlParts = parse_url($url);
        $currentParts = parse_url(Request::getDomainWithProtocol());

        if (!is_array($urlParts) || !is_array($currentParts)) {
            return false;
        }

        if (isset($urlParts['user']) || isset($urlParts['pass'])) {
            return false;
        }

        if (empty($urlParts['scheme']) || empty($urlParts['host'])) {
            return false;
        }

        if (empty($currentParts['scheme']) || empty($currentParts['host'])) {
            return false;
        }

        return strtolower((string)$urlParts['scheme']) === strtolower((string)$currentParts['scheme'])
            && strtolower((string)$urlParts['host']) === strtolower((string)$currentParts['host'])
            && $this->prgRedirectPort($urlParts) === $this->prgRedirectPort($currentParts);
    }

    /**
     * @param array{scheme?: string, port?: int} $parts
     */
    private function prgRedirectPort(array $parts): int
    {
        if (isset($parts['port'])) {
            return (int)$parts['port'];
        }

        return strtolower((string)($parts['scheme'] ?? '')) === 'https' ? 443 : 80;
    }

    /**
     * Метод устанавливает директорию, с которой нужно брать файлы шаблона (модуль или стандартный путь)
     *
     * @throws \Exception
     */
    public function configureTemplateDirProcedure()
    {
        /** @var Module $module */
        $module = $this->SL->getService(Module::class);
        /** @var Design $design */
        $design = $this->SL->getService(Design::class);
        /** @var Router $router */
        $router = $this->SL->getService(Router::class);

        if ($route = $router->getRouteByName($router->getCurrentRouteName())) {
            //$reflector = new \ReflectionClass($route['params']['controller']);

            // Сменим директорию шаблонов на директорию модуля
            if ($module->isModuleController($route['params']['controller'])) {
                $moduleTemplateDir = $module->generateModuleTemplateDir(
                    $module->getVendorName($route['params']['controller']),
                    $module->getModuleName($route['params']['controller'])
                );

                if (!empty($moduleTemplateDir)) {
                    $design->setModuleTemplatesDir($moduleTemplateDir);
                    $design->useModuleDir();
                }
            }
        }
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getPaymentMethods()
    {
        $entityFactory  = $this->SL->getService(EntityFactory::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $entityFactory->get(PaymentsEntity::class);
        $paymentMethodImages = $paymentsEntity->find(['enabled' => 1]);
        return ExtenderFacade::execute(__METHOD__, $paymentMethodImages, func_get_args());
    }

    /**
     * @param array<int|string, VisibleTreeItem> $items
     * @param array<int|string, VisibleTreeItem> $allItems
     * @param string $subItemsName
     */
    public function countVisible(array $items, array $allItems, string $subItemsName = 'subcategories')
    {
        foreach ($items as $item) {
            $parentId = $item->parent_id;
            if ($parentId !== null && isset($allItems[$parentId]) && !isset($allItems[$parentId]->count_children_visible)) {
                $allItems[$parentId]->count_children_visible = 0;
            }
            if ($parentId && $item->visible) {
                $allItems[$parentId]->count_children_visible++;
            }
            if (isset($item->{$subItemsName})) {
                /** @var array<int|string, VisibleTreeItem> $subItems */
                $subItems = $item->{$subItemsName};
                $this->countVisible($subItems, $allItems, $subItemsName);
            }
        }
    }
}
