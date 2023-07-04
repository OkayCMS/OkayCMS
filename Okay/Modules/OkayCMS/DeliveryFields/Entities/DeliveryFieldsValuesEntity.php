<?php

namespace Okay\Modules\OkayCMS\DeliveryFields\Entities;

use Okay\Core\Entity\Entity;

class DeliveryFieldsValuesEntity extends Entity
{
    protected static $table = 'okaycms__delivery_fields_values';

    protected static $fields = [
        'id',
        'field_id',
        'order_id',
        'value',
    ];
}