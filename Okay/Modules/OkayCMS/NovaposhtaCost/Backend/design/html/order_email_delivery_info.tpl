{if $delivery->settings['service_type'] == 'DoorsDoors' || $delivery->settings['service_type'] == 'WarehouseDoors'}
    {if $novaposhta_delivery_data->city_name}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px"><span>{$btr->order_np_city|escape}:</span></td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->city_name|escape}</span></td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->area_name}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px"><span>{$btr->order_np_area|escape}:</span></td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->area_name|escape}</span></td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->street}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px"><span>{$btr->order_np_street|escape}:</span></td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->street|escape}</span></td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->house}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px"><span>{$btr->order_np_house|escape}:</span></td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->house|escape}</span></td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->apartment}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px"><span>{$btr->order_np_apartment|escape}:</span></td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->apartment|escape}</span></td>
        </tr>
    {/if}
{else}
    {if $novaposhta_delivery_data->city->name}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px"><span>{$btr->order_np_city|escape}:</span></td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->city->name|escape}</span></td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->warehouse->name}
        <tr valign="top">
            <td class="es-p5t es-p5b" width="180px">
                <span>
                    {$btr->order_np_warehouse|escape}:
                </span>
            </td>
            <td class="es-p5t es-p5b"><span>{$novaposhta_delivery_data->warehouse->name}</span></td>
        </tr>
    {/if}
{/if}