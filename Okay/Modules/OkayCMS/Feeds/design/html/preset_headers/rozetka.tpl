<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>
<yml_catalog date="{date('Y-m-d H:i')}">
    <shop>

        <name>{$settings->site_name|escape}</name>

        {if $feed->settings['company']}
            <company>{$feed->settings['company']|escape}</company>
        {else}
            <company>{$settings->site_name|escape}</company>
        {/if}

        <url>{$rootUrl}</url>

        <platform>OkayCMS</platform>

        <version>{$config->version|escape} {$config->version_type|escape}</version>

        <currencies>
            {foreach $currencies as $c}
                <currency id="{$c->code|escape}" rate="{$c->rate_to/$c->rate_from*$main_currency->rate_from/$main_currency->rate_to}"/>
            {/foreach}
        </currencies>

        {$xml_categories}

        <offers>
