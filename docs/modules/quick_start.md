# Створення модуля

Розглянемо приклад створення модуля FAQ розробника OkayCMS.
Усі дії в цьому гайді (якщо не зазначено інше) виконуються в директорії `Okay/Modules/OkayCMS/FAQ`
(і в просторі імен `Okay\Modules\OkayCMS\FAQ`).

##### Ініціалізація модуля <a name="InitInitphp"></a>

Спочатку потрібно створити клас `Init\Init`, у якому описати встановлення та ініціалізацію модуля.
У методі `install()` створюємо або оновлюємо [таблицю для модуля](./table_migrate.md#migrateEntityTable)
та інші початкові налаштування. [Докладніше про клас Init](init.md).
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

* У методі `init()` виконуємо налаштування роботи модуля.
* Реєструємо [backend-контролери](./../controllers.md#backendControllersModules) і вказуємо їхні permissions для менеджера.
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

* Далі створюємо директорію `Backend` і в ній створюємо [контролери](./../controllers.md#backendControllersModules),
файли шаблонів, стилі, переклади — в точно такій самій структурі, [як у стандартному OkayCMS](./../files.md#backendFIles)
* Створюємо [frontend-контролери](./../controllers.md#frontControllersModules)
* Створюємо дизайн модуля, який розташований у директорії `design` і повторює
[стандартний дизайн OkayCMS](./../files.md#frontDesign)
* Описуємо класи [Entity](./../entities.md) модуля
* Створюємо файл `Init/routes.php`, у якому [описуємо маршрути](./../routes.md)
* Переходимо в admin-частину сайту, в розділ модулів і навпроти цього модуля натискаємо “Встановити”.
