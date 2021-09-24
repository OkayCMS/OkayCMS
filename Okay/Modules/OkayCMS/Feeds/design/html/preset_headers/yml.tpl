<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>
<yml_catalog date="{date('Y-m-d H:i')}">
    <shop>
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

        <store>{if $feed->settings['store']}true{else}false{/if}</store>

        <delivery>{if $feed->settings['delivery']}true{else}false{/if}</delivery>

        <pickup>{if $feed->settings['pickup']}true{else}false{/if}</pickup>

        <adult>{if $feed->settings['adult']}true{else}false{/if}</adult>

        <enable_auto_discounts>{if $feed->settings['enable_auto_discounts']}true{else}false{/if}</enable_auto_discounts>

        <currencies>
            {foreach $currencies as $c}
                <currency id="{$c->code|escape}" rate="{$c->rate_to/$c->rate_from*$main_currency->rate_from/$main_currency->rate_to}"/>
            {/foreach}
        </currencies>

        {$xml_categories}

        <offers>
