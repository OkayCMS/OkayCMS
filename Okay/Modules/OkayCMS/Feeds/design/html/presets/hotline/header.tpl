<?xml version='1.0' encoding='UTF-8'?>
<price>
    <date>{$smarty.now|date_format:'%Y-%m-%d %H:%M'}</date>

    {if $feed->settings['company']}
        <firmName>{$feed->settings['company']|escape}</firmName>
    {else}
        <firmName>{$settings->site_name|escape}</firmName>
    {/if}

    {if $feed->settings['firm_id']}
        <firmId>{$feed->settings['firm_id']}</firmId>
    {/if}

    {$xml_categories}

    <items>