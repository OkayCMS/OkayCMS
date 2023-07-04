<?php


namespace Okay\Modules\OkayCMS\DeliveryFields\Extenders;


use Okay\Core\FrontTranslations;
use Okay\Core\Request;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Validator;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsEntity;

class ValidateHelperExtender implements ExtensionInterface
{
    private FrontTranslations $frontTranslations;
    private DeliveryFieldsEntity $deliveryFieldsEntity;
    private Request $request;
    private Validator $validator;

    public function __construct(
        FrontTranslations $frontTranslations,
        EntityFactory $entityFactory,
        Request $request,
        Validator $validator
    ) {
        $this->frontTranslations = $frontTranslations;
        $this->deliveryFieldsEntity = $entityFactory->get(DeliveryFieldsEntity::class);
        $this->request = $request;
        $this->validator = $validator;
    }

    /**
     * @param $error
     * @return string|null
     * @throws \Exception
     *
     * Валідуємо поля способів доставки в кошику.
     */
    public function extendGetCartValidateError($error): ?string
    {
        if (!empty($error)) {
            return $error;
        }

        $deliveryId = $this->request->post('delivery_id');
        $deliveryFields = $this->deliveryFieldsEntity->mappedBy('id')->find([
            'delivery_id' => (array)$deliveryId,
        ]);
        if (empty($deliveryFields)) {
            return $error;
        }

        $requestedDeliveryFields = $this->request->post('delivery_fields');
        if (empty($requestedDeliveryFields[$deliveryId])) {
            return $error;
        }

        foreach ($requestedDeliveryFields[$deliveryId] as $fieldId => $fieldValue) {
            if (isset($deliveryFields[$fieldId])) {
                if (!$this->validator->isSafe($fieldValue, (bool)$deliveryFields[$fieldId]->required)) {
                    return sprintf(
                        $this->frontTranslations->getTranslation('okaycms__delivery_fields_error'),
                        $deliveryFields[$fieldId]->name
                    );
                }
            }
        }

        return $error;
    }
}