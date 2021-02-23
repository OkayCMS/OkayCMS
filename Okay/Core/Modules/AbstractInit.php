<?php


namespace Okay\Core\Modules;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\Config;
use Okay\Core\Design;
use Okay\Core\DesignBlocks;
use Okay\Core\Database;
use Okay\Core\Discounts;
use Okay\Core\Entity\Entity;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Managers;
use Okay\Core\QueryFactory;
use Okay\Core\ServiceLocator;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\ModulesEntity;
use Okay\Core\ManagerMenu;
use Okay\Core\Modules\Extender\ExtenderFacade;

abstract class AbstractInit
{
    private $allowedTypes = [
        MODULE_TYPE_PAYMENT,
        MODULE_TYPE_DELIVERY,
        MODULE_TYPE_XML,
    ];

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Module
     */
    private $module;
    
    /**
     * @var Modules
     */
    private $modules;
    
    /**
     * @var Managers
     */
    private $managers;
    
    /**
     * @var Database
     */
    private $db;

    /**
     * @var QueryFactory
     */
    private $queryFactory;
    
    /**
     * @var ModulesEntitiesFilters
     */
    private $entitiesFilters;

    /**
     * @var EntityMigrator
     */
    private $entityMigrator;

    /**
     * @var UpdateObject
     */
    private $updateObject;

    /**
     * @var ExtenderFacade
     */
    private $extenderFacade;

    /**
     * @var ManagerMenu
     */
    private $managerMenu;

    /**
     * @var Image
     */
    private $image;

    /**
     * @var FrontTemplateConfig
     */
    private $frontTemplateConfig;

    /**
     * @var Design
     */
    private $design;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Discounts
     */
    private $discounts;

    /**
     * @var int id модуля в базе
     */
    private $moduleId;
    private $vendor;
    private $moduleName;
    
    /** @var array Список зарегестрированных контроллеров админки */
    private $backendControllers = [];
    
    public function __construct($moduleId, $vendor, $moduleName)
    {
        if (!is_int($moduleId)) {
            throw new \Exception('"$moduleId" must be integer');
        }
        
        $serviceLocator        = ServiceLocator::getInstance();
        $this->entityFactory   = $serviceLocator->getService(EntityFactory::class);
        $this->queryFactory    = $serviceLocator->getService(QueryFactory::class);
        $this->entityMigrator  = $serviceLocator->getService(EntityMigrator::class);
        $this->module          = $serviceLocator->getService(Module::class);
        $this->modules         = $serviceLocator->getService(Modules::class);
        $this->managers        = $serviceLocator->getService(Managers::class);
        $this->db              = $serviceLocator->getService(Database::class);
        $this->entitiesFilters = $serviceLocator->getService(ModulesEntitiesFilters::class);
        $this->updateObject    = $serviceLocator->getService(UpdateObject::class);
        $this->extenderFacade  = $serviceLocator->getService(ExtenderFacade::class);
        $this->managerMenu     = $serviceLocator->getService(ManagerMenu::class);
        $this->image           = $serviceLocator->getService(Image::class);
        $this->frontTemplateConfig = $serviceLocator->getService(FrontTemplateConfig::class);
        $this->design          = $serviceLocator->getService(Design::class);
        $this->config          = $serviceLocator->getService(Config::class);
        $this->discounts       = $serviceLocator->getService(Discounts::class);
        $this->moduleId        = $moduleId;
        $this->vendor          = $vendor;
        $this->moduleName      = $moduleName;
    }

    /**
     * Метод, который вызывается во время утавноки модуля
     */
    abstract public function install();

    /**
     * Метод, который вызывается для каждого модуля во время каждого запуска системы
     */
    abstract public function init();

    /**
     * Отрабатывает при установке модуля и задает ему статус системного, что означает отсутствие его в списке модулей для менеджеров
     * у которых недостаточно прав на просмотр модулей данного типа
     */
    protected function setSystem()
    {
        /** @var ModulesEntity $modulesEntity */
        $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
        $modulesEntity->update($this->moduleId, ['system' => 1]);
    }
    
    /**
     * Регистрация блока в админке. Чтобы узнать имя блока, к которому хотите зацепиться,
     * нужно в конфиге включить директиву dev_mode = true,
     * зайти в админку на нужную страницу и можно будет увидеть красные лейблы, при наведении на них мышкой
     * они подсвечивают область, действия блока.
     * Все данные из вашего tpl файла будут подставляться в конец выделеного блока.
     *
     * @param string $blockName название блока
     * @param string $blockTplFile имя tpl файла блока из директории Backend/design/html модуля
     * @param callable $callback ф-ция которую нужно вызвать перед отрисовкой шортблока. Может использоваться для 
     * передачи в дизайн данных, нужных для отрисовки шортблока. Можно указывать как аргументы с указанием 
     * type hint Services, Entities etc.
     * @throws \Exception
     */
    protected function addBackendBlock($blockName, $blockTplFile, $callback = null)
    {
        $blockTplFile = pathinfo($blockTplFile, PATHINFO_BASENAME);
        $blockTplFile = $this->module->getModuleDirectory($this->vendor, $this->moduleName) . 'Backend/design/html/' . $blockTplFile;
        $this->addDesignBlock($blockName, $blockTplFile, $callback);
    }

    /**
     * Регистрация блока в дизайне клиентского шаблона. Чтобы узнать имя блока, к которому хотите зацепиться,
     * нужно в конфиге включить директиву dev_mode = true,
     * зайти в клиентской части сайта на нужную страницу и можно будет увидеть красные лейблы, при наведении на них мышкой
     * они подсвечивают область, действия блока.
     * Все данные из вашего tpl файла будут подставляться в конец выделеного блока.
     *
     * @param string $blockName название блока
     * @param string $blockTplFile имя tpl файла блока из директории Backend/design/html модуля
     * @param callable $callback ф-ция которую нужно вызвать перед отрисовкой шортблока. Может использоваться для
     * передачи в дизайн данных, нужных для отрисовки шортблока. Можно указывать как аргументы с указанием
     * type hint Services, Entities etc.
     * @throws \Exception
     */
    protected function addFrontBlock($blockName, $blockTplFile, $callback = null)
    {
        $blockTplFile = pathinfo($blockTplFile, PATHINFO_BASENAME);
        $themeModuleHtmlDir = __DIR__.'/../../../design/'.$this->frontTemplateConfig->getTheme().'/modules/'.$this->vendor.'/'.$this->moduleName.'/html/';
        if (file_exists($themeModuleHtmlDir.$blockTplFile)) {
            $blockTplFile = $themeModuleHtmlDir.$blockTplFile;
        } else {
            $blockTplFile = $this->module->getModuleDirectory($this->vendor, $this->moduleName) . 'design/html/' . $blockTplFile;
        }

        $this->addDesignBlock($blockName, $blockTplFile, $callback);
    }
    
    /**
     * Метод расширяет коллекцию объектов доступную для использования в файле ajax/update_object.php,
     * который обновляет определенную по алиасу сущность повредством AJAX запроса из админ панели сайта
     *
     * @param $alias - уникальный псевдоним, который идентифицирует сущность (указывается в атрибуте data-controller="алиас" тега в админ панели)
     * @param $permission - права доступа к псевдониму для менеджера
     * @param $entityClassName - полное имя сущности, которая будет обновляться
     * @throws \Exception
     */
    protected function extendUpdateObject($alias, $permission, $entityClassName)
    {
        $this->updateObject->register($alias, $permission, $entityClassName);
    }

    /**
     * @param string $originalImgDirDirective название директивы конфига, которая содержит путь к директории оригиналов изображений
     * @param string $resizedImgDirDirective название директивы конфига, которая содержит путь к директории нарезок изображений
     * @throws \Exception
     */
    protected function addResizeObject($originalImgDirDirective, $resizedImgDirDirective)
    {
        $this->image->addResizeObject($originalImgDirDirective, $resizedImgDirDirective);
    }
    
    /**
     * Данный метод позволяет расширять меню админ панели посредством добавления новых пунктов меню в оную
     *
     * @param $firstLevelName - ленг корневого пункта меню. Если указать существующий, то пункты меню второго уровня добавляться в конец списка внутри существующего пункта меню
     * @param $menuItemsByControllers - ассоциативный массив с ленгами пунктов меню в качестве ключа и соответствующими им контроллерами в качестве значений
     * @param $icon - путь к файлу относительно папки Backend модуля или текст svg картинки
     * @throws \Exception
     *
     * @example $this->extendBackendMenu('first_level_menu_name', [
            'lang_name_menu_item_1' => ['SomeOneAdmin'],
            'lang_name_menu_item_2' => ['SomeTwoAdmin', 'SomeThreeAdmin'],
        ], 'icon');
     */
    protected function extendBackendMenu($firstLevelName, array $menuItemsByControllers, $icon = null)
    {
        $moduleDirectory = $this->module->getModuleDirectory($this->vendor, $this->moduleName);

        foreach($menuItemsByControllers as $item => $controllers) {
            foreach($controllers as $key => $controller) {
                $menuItemsByControllers[$item][$key] = $this->module->getBackendControllerName($this->vendor, $this->moduleName, $controller);
            }
        }

        if (!empty($icon) && is_file($moduleDirectory.$icon)) {
            $icon = $moduleDirectory.$icon;
        }

        $this->managerMenu->extendMenu($firstLevelName, $menuItemsByControllers, $icon);
    }

    /**
     * Добавление элемента меню быстрого редактирования для администратора.
     * 
     * @param string $dataProperty data атрибут который должен быть у html элемента, и при наведении на который будет
     * открываться данное меню
     * @param array ...$menuItems массив описаний ссылок меню
     * 
     * @example $this->extendBackendMenu('property', [
            'controller' => 'Vendor.Module.Controller',
            'translation' => 'translation_var_add',
        ], [
            'controller' => 'Vendor.Module.Controller',
            'translation' => 'translation_var_edit',
            'params' => [
                'id' => 'id',
            ],
            'action' => 'edit',
        ]);
     * При наведении на элемент с атрибутом data-property="1" будут построены ссылки на добавление сущности через 
     * контроллер Vendor.Module.Controller и на редактирование с GET параметром id=1 (указанным в data-property).
     */
    protected function addFastMenuItem($dataProperty, ...$menuItems)
    {
        call_user_func_array([$this->managerMenu, 'addFastMenuItem'], array_merge([$dataProperty], $menuItems));
    }

    /**
     * Данный метод регистрирует новый обработчик для расширениия классов ядра.
     * Подхватывает результат работы указанного метода класса системы
     * и навешивает на него пост обработку. Если же на данный метод ядра уже навешены
     * подобные обработки, то они исполнятся в порядке регистрации модулей
     * и каждый последующий работает с результатами полученныйми от предыдущего обработчика.
     *
     * Рекомендуется в данном методе регистрировать исключительно чистые функции, в которых
     * отсутствует интеграционная логика.
     *
     * Для процедур следует использовать метод AbstractInit::registerQueueHandler
     *
     * @param $expandable - ['class' => Имя_Расширяемого_Класса, 'method' => Метод_Расширяемого_Класса]
     * @param $extension - ['class' => Имя_Класса_Расширения, 'method' => Метод_Расширения]
     * @throws \Exception
     */
    protected function registerChainExtension($expandable, $extension)
    {
        $this->extenderFacade->newChainExtension($expandable, $extension);
    }

    /**
     * Метод работает аналогично AbstractInit::registerChainHandler, за исключением того, что
     * зарегестрированные в нем обработчики не модифицирует входящие данные, а лишь работает
     * с данными, которые были преобразованы всеми исполнителями AbstractInit::registerChainHandler,
     * подвязанными к указанному методу класса ядра, если же таких обработчиков нет, то в качестве
     * данных принимается непреобразованное возвращаемое методом значение
     *
     * Рекомендуется в данном методе регистрировать процедуры, для функций с возвращаемыми значениями
     * следует использовать AbstractInit::registerChainHandler
     *
     * @param $expandable ['class' => Имя_Расширяемого_Класса, 'method' => Метод_Расширяемого_Класса]
     * @param $extension ['class' => Имя_Класса_Расширения, 'method' => Метод_Расширения]
     * @throws \Exception
     */
    protected function registerQueueExtension($expandable, $extension)
    {
        $this->extenderFacade->newQueueExtension($expandable, $extension);
    }

    /**
     * Регистрация фильтра для уже существующих в системе сущностей, расположенных в директории Okay\Entities
     *
     * @param $entityClassName - имя класса для когорого регистрируется новый фильтр
     * @param $filterName - имя нового фильтра, которое будет использоваться в массиве совместно с остальным фильтрами
     * @param $filterClassName - класс в котором описана реализация нового фильтра
     * @param $filterMethod - метод описывающий реализацию нового фильтра
     * @throws \Exception
     */
    protected function registerEntityFilter($entityClassName, $filterName, $filterClassName, $filterMethod)
    {
        $this->entitiesFilters->registerFilter($entityClassName, $filterName, $filterClassName, $filterMethod);
    }

    /**
     * Создание таблицы новой сущности. Саму сущность регистрировать нигде не нужно,
     * просто вызываем по неймспейсу из EntityFactory
     *
     * @var string $entityClassName Имя класса сущности, которая создается модулем
     * @var array $fields массив объектов Okay\Core\Modules\EntityField, описывающих поля таблицы
     * @throws \Exception
     * 
     * @example $this->migrateEntityTable(MyEntityClass::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
            (new EntityField('visible'))->setTypeTinyInt(1),
            (new EntityField('position'))->setTypeInt(11),
        ])
     */
    protected function migrateEntityTable($entityClassName, array $fields)
    {
        $this->entityMigrator->migrateEntityTable($entityClassName, $fields);
    }

    /**
     * @param string $tableName название таблицы
     * @param array $fields массив объектов Okay\Core\Modules\EntityField, описывающих поля таблицы
     * 
     * Создание таблицы в БД. В основном используется для создания таблиц связей.
     * Таблицы сущностей лучше создавать через migrateEntityTable()
     */
    protected function migrateCustomTable($tableName, array $fields)
    {
        $this->entityMigrator->migrateCustomTable($tableName, $fields);
    }

    /**
     * Регистрация дополнительных полей к существующим сущностям
     * (в базу не добавляются, только учавствуют в селекте и фильтрации)
     * Вызывать метод нужно в методе init()
     *
     * @param string $entityClassName
     * @param string $fieldName
     * @param bool $isLang является ли это поле ленговым
     * @throws \Exception
     */
    protected function registerEntityField($entityClassName, $fieldName, $isLang = false)
    {
        /** @var Entity $entityClassName */
        if ($isLang === true) {
            $entityClassName::addLangField($fieldName);
            return;
        }

        $entityClassName::addField($fieldName);
    }

    /**
     * @param $entityClassName
     * @param $fieldName
     * 
     * Регистрация дополнительных полей к существующим сущностям.
     * Это поле не обязательно должно быть в таблице сущности, это может быть дополнительный запрос и результат как колонка сущности
     */
    protected function registerEntityAdditionalField($entityClassName, $fieldName)
    {
        $entityClassName::addAdditionalField($fieldName);
    }

    /**
     * Регистрация ленговой таблицы для указанного Entity. Нужно в случае, если стандартный Entity был не мультиязычна, 
     * а нужно чтобы он стал мультиязычным.
     * 
     * @param $entityClassName
     * @param $langTable
     * @param $langObject
     */
    protected function registerEntityLangInfo($entityClassName, $langTable, $langObject)
    {
        /** @var Entity $entityClassName */
        $entityClassName::setLangTable($langTable);
        $entityClassName::setLangObject($langObject);
    }

    /**
     * Добавление дополнительных полей в БД к существующим сущностям
     * Вызывать метод стоит в методе install()
     *
     * @param string $entityClassName
     * @param EntityField $field
     * @throws \Exception
     *
     * @example $field = new EntityField('field_name');
        $field->setTypeTinyInt(1);
        $this->migrateEntityField(CategoriesEntity::class, $field);
     */
    protected function migrateEntityField($entityClassName, EntityField $field)
    {
        $this->entityMigrator->migrateField($entityClassName, $field);
    }
    
    /**
     * Имя контроллера, который будет в админке обрабатываться как основной.
     * Когда со списка модулей переход внутрь модуля, попадаем на этот контроллер
     *
     * @param $className 
     * @throws \Exception
     */
    protected function setBackendMainController($className)
    {
        if ($this->validateBackendController($className)) {

            /** @var ModulesEntity $modulesEntity */
            $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
            $modulesEntity->update($this->moduleId, ['backend_main_controller' => $className]);
        }
    }

    /**
     * Добавление разрешения, в общий массив разрешений для мереджеров.
     * Нужно использовать если нужно разрешение, но контроллера для него нет.
     *
     * @param string $permission название разрешения
     * @throws \Exception
     */
    protected function addPermission($permission)
    {
        $this->managers->addModulePermission((string)$permission, $this->vendor . '/' . $this->moduleName);
    }

    /**
     * Добавление связки разрешения и контроллера админки.
     * Отдельно регистрировать разрешение через addPermission() не нужно
     *
     * @param string $controllerClass имя класса контроллера
     * @param string $permission название разрешения
     * @throws \Exception
     */
    protected function addBackendControllerPermission($controllerClass, $permission)
    {
        if ($this->validateBackendController($controllerClass)) {
            $this->addPermission($permission);
            $controllerClass = $this->module->getBackendControllerName($this->vendor, $this->moduleName, $controllerClass);
            $this->managers->addModuleControllerPermission($controllerClass, (string)$permission);
        }
    }

    /**
     * Добавление контроллера админки в общий массив контроллеров.
     * Контроллер должен находиться в Okay\Modules\Vendor\Module\Backend\Controllers\ControllerName
     * должен наследоваться от Okay\Admin\Controllers\IndexAdmin
     * и содержать метод fetch(), как стандартный контроллер админки
     *
     * @param string $controllerClass имя класса контроллера
     * @throws \Exception
     */
    protected function registerBackendController($controllerClass)
    {
        if (is_dir($this->module->getBackendControllersDirectory($this->vendor, $this->moduleName))) {

            // Вырезаем namespace из названия контроллера
            $controllerClass = str_replace(
                $this->module->getBackendControllersNamespace($this->vendor, $this->moduleName) . '\\',
                '',
                $controllerClass
            );

            if ($this->validateBackendController($controllerClass)) {
                $this->backendControllers[] = $controllerClass;
            }
        }
    }

    /**
     * Установки типа модуля. Типы влияют на группировку модулей (категоризация).
     * Также модули нужного типа выводятся в определённых частях системы, например:
     * Модули типа MODULE_TYPE_DELIVERY выводятся в админке в способе доставки, как выбор модуля доставки
     * Модули типа MODULE_TYPE_PAYMENT выводятся в админке в способе оплаты, как выбор платёжного модуля.
     * В системе есть константы, начинающиеся на MODULE_TYPE_*, нужно использовать только их!
     *
     * @param string $type
     * @throws \Exception
     */
    protected function setModuleType($type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new \Exception("Type \"$type\" not supported");
        }

        /** @var ModulesEntity $modulesEntity */
        $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
        
        $modulesEntity->update($this->moduleId, ['type' => $type]);
    }

    /**
     * Метод возвращает массив котроллеров модулей для админки
     *
     * @return array
     */
    public function getBackendControllers()
    {
        return $this->backendControllers;
    }

    /**
     * Валидация контроллера админки
     *
     * @param $className
     * @return bool
     * @throws \Exception
     */
    private function validateBackendController($className)
    {
        $fullControllerName = $this->module->getBackendControllersDirectory($this->vendor, $this->moduleName) . $className . '.php';
        if (!is_file($fullControllerName)) {
            throw new \Exception("Controller \"$fullControllerName\" not exists");
        }

        $backendControllersNamespace = $this->module->getBackendControllersNamespace($this->vendor, $this->moduleName);
        if (!is_subclass_of($backendControllersNamespace . '\\' . $className, IndexAdmin::class)) {
            throw new \Exception("Controller \"$fullControllerName\" must be a subclass of \"". IndexAdmin::class . "\"");
        }
        
        if (!method_exists($backendControllersNamespace . '\\' . $className, 'fetch')) {
            throw new \Exception("Controller \"$fullControllerName\" must have a method \"fetch()\"");
        }
        
        return true;
    }

    /**
     * Метод регистрирует блок для нужной части дизайна
     *
     * @param $blockName
     * @param $blockTplFile
     * @param $callback
     * @throws \Exception
     */
    private function addDesignBlock($blockName, $blockTplFile, $callback = null)
    {
        $serviceLocator = ServiceLocator::getInstance();

        /** @var DesignBlocks $designBlocks */
        $designBlocks = $serviceLocator->getService(DesignBlocks::class);
        $designBlocks->registerBlock($blockName, $blockTplFile, $callback);
    }

    public function registerPurchaseDiscountSign($sign, $name, $description)
    {
        $this->discounts->registerPurchaseSign($sign, $name, $description);
    }

    public function registerCartDiscountSign($sign, $name, $description)
    {
        $this->discounts->registerCartSign($sign, $name, $description);
    }
}