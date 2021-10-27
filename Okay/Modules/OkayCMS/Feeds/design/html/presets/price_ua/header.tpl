<?xml version='1.0' encoding='UTF-8'?>
<price date="{date('Y-m-d H:i')}">
    {if $feed->settings['feed_name']}
        <name>{$feed->settings['feed_name']|escape}</name>
    {/if}

    {if $feed->settings['company']}
        <company>{$feed->settings['company']|escape}</company>
    {/if}

    <agency>OkayCMS</agency>

    <email>info@okay-cms.com</email>

    <url>{$rootUrl}</url>

    <platform>OkayCMS</platform>

    <version>{$config->version|escape}</version>

    <currencies>
        {foreach $currencies as $c}
            <currency id="{$c->code|escape}" rate="{$c->rate_to/$c->rate_from*$main_currency->rate_from/$main_currency->rate_to}"/>
        {/foreach}
    </currencies>

    {$xml_categories}

    <items>