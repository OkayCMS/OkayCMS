{foreach $yandexFeeds as $feed}
    <option value="{Okay\Modules\OkayCMS\YandexXML\Init\Init::TO_FEED_FIELD}@{$feed->id}" data-label="{$btr->getTranslation('okaycms__yandex_xml__import_field')} {$feed@iteration}.{$feed->name}">
        {$btr->getTranslation('okaycms__yandex_xml__import_field')} {$feed@iteration}.{$feed->name}
    </option>
{/foreach}