{if $delivery->settings['service_type'] == 'DoorsDoors' || $delivery->settings['service_type'] == 'WarehouseDoors'}
    {if $novaposhta_delivery_data->city_name}
        <tr>
            <td>
                <span data-language="np_order_city">{$lang->np_order_city|escape}</span>
            </td>
            <td>{$novaposhta_delivery_data->city_name|escape}</td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->area_name}
        <tr>
            <td>
                <span data-language="np_order_area">{$lang->np_order_area|escape}</span>
            </td>
            <td>{$novaposhta_delivery_data->area_name|escape}</td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->street}
        <tr>
            <td>
                <span data-language="np_order_street">{$lang->np_order_street|escape}</span>
            </td>
            <td>{$novaposhta_delivery_data->street|escape}</td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->house}
        <tr>
            <td>
                <span data-language="np_order_house">{$lang->np_order_house|escape}</span>
            </td>
            <td>{$novaposhta_delivery_data->house|escape}</td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->apartment}
        <tr>
            <td>
                <span data-language="np_order_apartment">{$lang->np_order_apartment|escape}</span>
            </td>
            <td>{$novaposhta_delivery_data->apartment|escape}</td>
        </tr>
    {/if}
{else}
    {if $novaposhta_delivery_data->city->name}
        <tr>
            <td>
                <span data-language="np_order_city">{$lang->np_order_city}</span>
            </td>
            <td>{$novaposhta_delivery_data->city->name|escape}</td>
        </tr>
    {/if}
    {if $novaposhta_delivery_data->warehouse->name}
        <tr>
            <td>
                <span data-language="np_order_warehouse">{$lang->np_order_warehouse|escape}</span>
            </td>
            <td>{$novaposhta_delivery_data->warehouse->name}</td>
        </tr>
    {/if}
{/if}