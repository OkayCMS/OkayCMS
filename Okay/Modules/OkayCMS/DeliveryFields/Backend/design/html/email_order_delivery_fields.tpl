{foreach $deliveryFields as $field}
    <tr valign="top">
        <td class="es-p5t es-p5b" width="180px"><span>{$field->name|escape}:</span></td>
        <td class="es-p5t es-p5b"><span>{$field->value|escape}</span></td>
    </tr>
{/foreach}