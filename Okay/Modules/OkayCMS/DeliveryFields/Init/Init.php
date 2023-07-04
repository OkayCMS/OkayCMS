<?php


namespace Okay\Modules\OkayCMS\DeliveryFields\Init;


use Okay\Admin\Helpers\BackendDeliveriesHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Core\QueryFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\DeliveriesEntity;
use Okay\Helpers\DeliveriesHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Helpers\ValidateHelper;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsEntity;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsValuesEntity;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\DeliveryFieldsExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\OrdersHelperExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\ValidateHelperExtender;

class Init extends AbstractInit
{
    const PERMISSION = 'okay_cms__delivery_fields';
    const FIELD_DELIVERY_RELATION_TABLE = '__okaycms__delivery_fields_relations';

    public function install()
    {
        $this->setBackendMainController('DeliveryFieldsAdmin');
        $this->migrateEntityTable(DeliveryFieldsEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
            (new EntityField('visible'))->setTypeTinyInt(1),
            (new EntityField('required'))->setTypeTinyInt(1),
            (new EntityField('position'))->setTypeInt(11),
        ]);

        $orderIdField = (new EntityField('order_id'))->setTypeInt(11);
        $this->migrateEntityTable(DeliveryFieldsValuesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('value'))->setTypeVarchar(255)->setIsLang(),
            (new EntityField('field_id'))->setTypeInt(11)->setIndexUnique(null, $orderIdField),
            $orderIdField,
        ]);

        $deliveryIdField = (new EntityField('delivery_id'))->setTypeInt(11, false);
        $this->migrateCustomTable(self::FIELD_DELIVERY_RELATION_TABLE, [
            (new EntityField('field_id'))->setTypeInt(11, false)->setIndexUnique(null, $deliveryIdField),
            $deliveryIdField,
        ]);

        $this->initDeliveryFields();
    }

    public function init()
    {
        $this->addPermission(self::PERMISSION);
        $this->registerBackendController('DeliveryFieldsAdmin');
        $this->addBackendControllerPermission('DeliveryFieldsAdmin', self::PERMISSION);

        $this->addBackendBlock('email_order_admin_contact_info','email_order_delivery_fields.tpl');
        $this->addBackendBlock('order_print_user_info', 'order_print.tpl');

        $this->addFrontBlock('front_cart_delivery', 'cart_delivery_fields.tpl');
        $this->addFrontBlock('front_email_order_user_contact_info', 'email_order_delivery_fields.tpl');
        $this->addFrontBlock('front_scripts_after_validate', 'validation.js');

        $this->registerChainExtension(
            [DeliveriesHelper::class, 'getCartDeliveriesList'],
            [DeliveryFieldsExtender::class, 'extendGetCartDeliveriesList']
        );

        $this->registerQueueExtension(
            [OrdersHelper::class, 'finalCreateOrderProcedure'],
            [OrdersHelperExtender::class, 'extendFinalCreateOrderProcedure']
        );

        $this->registerQueueExtension(
            [OrdersHelper::class, 'getOrderPurchasesList'],
            [OrdersHelperExtender::class, 'extendGetOrderPurchasesList']
        );

        $this->registerChainExtension(
            [ValidateHelper::class, 'getCartValidateError'],
            [ValidateHelperExtender::class, 'extendGetCartValidateError']
        );

        $this->registerQueueExtension(
            [BackendOrdersHelper::class, 'findOrderDelivery'],
            [BackendExtender::class, 'extendFindOrderDelivery']
        );

        $this->registerQueueExtension(
            [BackendOrdersHelper::class, 'update'],
            [BackendExtender::class, 'extendUpdateOrder']
        );

        $this->registerQueueExtension(
            [BackendDeliveriesHelper::class, 'delete'],
            [BackendExtender::class, 'extendDeleteDelivery']
        );
    }

    private function initDeliveryFields()
    {
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        $queryFactory = $SL->getService(QueryFactory::class);
        $languages = $SL->getService(Languages::class);
        $deliveryFieldsEntity = $entityFactory->get(DeliveryFieldsEntity::class);

        if ($deliveryFieldsEntity->count() == 0) {
            $names = [
                'ua' => 'Адреса',
                'en' => 'Address',
                'ru' => 'Адрес',
            ];

            $currentLangId = $languages->getLangId();

            $select = $queryFactory->newSelect();
            $deliveriesIds = $select->cols(['id'])
                ->from(DeliveriesEntity::getTable())
                ->results('id');

            $deliveryFieldId = $deliveryFieldsEntity->add([
                'name' => $names[$languages->getLangLabel()] ?? 'Адреса',
                'visible' => 1,
                'required' => 0,
            ]);

            foreach ($languages->getAllLanguages() as $l) {
                if ($l->id != $currentLangId) {
                    $languages->setLangId($l->id);
                    $deliveryFieldsEntity->update($deliveryFieldId, [
                        'name' => $names[$l->label] ?? 'Адреса',
                    ]);
                }
            }

            foreach ($deliveriesIds as $deliveryId) {
                $deliveryFieldsEntity->addFieldDelivery((int)$deliveryFieldId, (int)$deliveryId);
            }

            // Переносимо поле адресу в модуль
            $query = $queryFactory->newSqlQuery();
            $query->setStatement(
                    'INSERT INTO __okaycms__delivery_fields_values (value, field_id, order_id)
                    SELECT address, :field_id, id FROM __orders WHERE address != ""'
                )->bindValue('field_id', $deliveryFieldId)
                ->execute();

            $languages->setLangId($currentLangId);
        }
    }
}