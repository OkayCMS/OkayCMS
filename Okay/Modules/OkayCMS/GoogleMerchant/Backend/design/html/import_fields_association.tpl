{foreach $googleFeeds as $feed}
    <option value="{Okay\Modules\OkayCMS\GoogleMerchant\Init\Init::TO_FEED_FIELD}@{$feed->id}" data-label="{$btr->getTranslation('okaycms__google_merchant__import_field')} {$feed@iteration}.{$feed->name}">
        {$btr->getTranslation('okaycms__google_merchant__import_field')} {$feed@iteration}.{$feed->name}
    </option>
{/foreach}