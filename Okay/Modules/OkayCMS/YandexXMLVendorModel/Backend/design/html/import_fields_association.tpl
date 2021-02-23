{foreach $yandexVendorModelFeeds as $feed}
    <option value="{Okay\Modules\OkayCMS\YandexXMLVendorModel\Init\Init::TO_FEED_FIELD}@{$feed->id}" data-label="{$btr->getTranslation('okaycms__yandex_xml_vendor_model__import_field')} {$feed@iteration}.{$feed->name}">
        {$btr->getTranslation('okaycms__yandex_xml_vendor_model__import_field')} {$feed@iteration}.{$feed->name}
    </option>
{/foreach}