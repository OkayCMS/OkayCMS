# Создание модуля

Рассмотрим пример созданим модуля FAQ разработчика OkayCMS.
Все действия в этом гайде (если инного не указанно) выполняются в директории `Okay/Modules/OkayCMS/FAQ`
(и в namespace `Okay\Modules\OkayCMS\FAQ`).

##### Инициализация модуля <a name="InitInitphp"></a>

Для начала нужно создать класс `Init\Init`, в котором нужно описать установку и инициализацию модуля.
В методе install() выполняем [миграцию таблицы для модуля](./table_migrate.md#migrateEntityTable).
и прочие первоначальные настройки. [Подробнее о классе Init](init.md).
```php
public function install()
{
    $this->setBackendMainController('FAQsAdmin');
    $this->migrateEntityTable(FAQEntity::class, [
        (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
        (new EntityField('question'))->setTypeText()->setIsLang(),
        (new EntityField('answer'))->setTypeText()->setIsLang()->setNullable(),
        (new EntityField('visible'))->setTypeTinyInt(1),
        (new EntityField('position'))->setTypeInt(11),
    ]);
}
```

* В методе init() выполняем настройку работы модуля.
* Регистрируем [бек-контроллеры](./../controllers.md#backendControllersModules), и указываем их разрешения для менеджера.
```php
public function init()
{
    $this->registerBackendController('FAQsAdmin');
    $this->addBackendControllerPermission('FAQsAdmin', 'okaycms__faq__faq');

    $this->registerBackendController('FAQAdmin');
    $this->addBackendControllerPermission('FAQAdmin', 'okaycms__faq__faq');

    $this->extendUpdateObject('OkayCMS.FAQ.FAQEntity', 'okaycms__faq__faq', FAQEntity::class);

    $this->extendBackendMenu('left_faq_title', [
        'left_faq_title' => ['FAQsAdmin', 'FAQAdmin'],
    ]);
}
```

* Далее создаем директорию `Backend` и в ней создаём [контроллеры](./../controllers.md#backendControllersModules), 
файлы шаблона, стили, переводы, в точно такой же структуре [как в стандартном OkayCMS](./../files.md#backendFIles)
* Создаем [фронт-контроллеры](./../controllers.md#frontControllersModules)
* Создаем дизайн модуля, который распологается в директории `design` и повторяет 
[стандартный дизайн OkayCMS](./../files.md#frontDesign)
* Описываем классы [Entity](./../entities.md) модуля
* Создаем файл `Init/routes.php` в котором [описываем маршруты](./../routes.md)
* Переходим в админ-часть сайта, в раздел модулей и напротив этого модуля нажимаем установить.
