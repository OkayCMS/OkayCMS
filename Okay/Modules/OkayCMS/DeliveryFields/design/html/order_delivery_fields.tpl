{foreach $deliveryFields as $field}
    <tr>
        <td>
            <span>{$field->name|escape}</span>
        </td>
        <td>{$field->value|escape}</td>
    </tr>
{/foreach}
