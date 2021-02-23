<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>
<yml_catalog date="{date('Y-m-d H:i')}">
<shop>
    <name>{$settings->site_name}</name>
    {if $settings->okaycms__yandex_xml__company}
        <company>{$settings->okaycms__yandex_xml__company|escape}</company>
    {/if}
    <agency>OkayCMS</agency>
    <email>info@okay-cms.com</email>
    <url>{$rootUrl}</url>
    <platform>OkayCMS</platform>
    <version>{$config->version} {$config->version_type}</version>
    <currencies>
        {foreach $currencies as $c}
            <currency id="{$c->code}" rate="{$c->rate_to/$c->rate_from*$main_currency->rate_from/$main_currency->rate_to}"/>
        {/foreach}
    </currencies>

    <categories>
    {function name=categories_tree}
        {if $categories}
            {foreach $categories as $c}
                <category id="{$c->id}"{if $c->parent_id>0} parentId="{$c->parent_id}"{/if}>{$c->name|escape}</category>
                {if $c->subcategories && $c->count_children_visible && $level < 3}
                    {categories_tree categories=$c->subcategories}
                {/if}
            {/foreach}
        {/if}
    {/function}
    {categories_tree categories=$categories}
    </categories>
    {get_design_block block=OkayCMS_YandexXML_head}
        <offers>
            