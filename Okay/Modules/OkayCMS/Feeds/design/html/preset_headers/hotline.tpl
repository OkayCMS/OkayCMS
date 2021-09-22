<?xml version='1.0' encoding='UTF-8'?>
<price>

    {if $feed->settings['company']}
        <firmName>{$feed->settings['company']|escape}</firmName>
    {else}
        <firmName>{$settings->site_name|escape}</firmName>
    {/if}

    {$xml_categories}

    <items>