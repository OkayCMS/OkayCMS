{if $feed->id}
    {$meta_title = $feed->name scope=global}
{else}
    {$meta_title = $btr->okay_cms__feeds__feed_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$feed->id}
                    {$btr->okay_cms__feeds__feed_add|escape}
                {else}
                    {$feed->name|escape}
                {/if}
            </div>
            {if $feed->id}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="{url_generator route="OkayCMS.Feeds.Feed" url=$feed->url absolute=1}">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                        <span>{$btr->general_open|escape}</span>
                    </a>
                </div>
            {/if}
        </div>
    </div>
</div>

{*Вывод успешных сообщений*}
{if $message_success}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--success">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_success == 'added'}
                            {$btr->okay_cms__feeds__feed_added|escape}
                        {elseif $message_success == 'updated'}
                            {$btr->okay_cms__feeds__feed_updated|escape}
                        {/if}
                    </div>
                </div>
                {if $smarty.get.return}
                    <a class="alert__button" href="{$smarty.get.return}">
                        {include file='svg_icon.tpl' svgId='return'}
                        <span>{$btr->general_back|escape}</span>
                    </a>
                {/if}
            </div>
        </div>
    </div>
{/if}

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_error == 'url_exists'}
                            {$btr->general_exists|escape}
                        {elseif $message_error=='empty_name'}
                            {$btr->general_enter_title|escape}
                        {elseif $message_error == 'url_wrong'}
                            {$btr->general_not_underscore|escape}
                        {else}
                            {$message_error|escape}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
<form method="post" class="fn_fast_button" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />

    <div class="row">
        <div class="col-xs-12 ">
            <div class="boxed match_matchHeight_true">
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="">
                            <div class="heading_label">
                                {$btr->general_name|escape}
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="name" type="text" value="{$feed->name|escape}"/>
                                <input name="id" type="hidden" value="{$feed->id|escape}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-lg-6 col-md-10">
                                <div class="mt-h mb-h mt-2">
                                    <div class="input-group input-group--dabbl">
                                        <span class="input-group-addon input-group-addon--left">URL</span>
                                        <input name="url" class="form-control fn_url {if $feed->id}fn_disabled{/if}" {if $feed->id}readonly=""{/if} type="text" value="{$feed->url|escape}" />
                                        <input type="checkbox" id="block_translit" class="hidden" value="1" {if $feed->id}checked=""{/if}>
                                        <span class="input-group-addon fn_disable_url">
                                            {if $feed->id}
                                                <i class="fa fa-lock"></i>
                                            {else}
                                                <i class="fa fa-lock fa-unlock"></i>
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <div class="heading_label heading_label--required">
                                        <span>{$btr->okay_cms__feeds__feed_preset}</span>
                                    </div>
                                    <select class="selectpicker form-control mb-1" name="preset">
                                        {foreach $presets as $preset_name => $preset}
                                            <option value="{$preset_name}" {if $feed->preset == $preset_name} selected {/if}>{$preset_name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                        {get_design_block block="okay_cms__feeds__feed__general"}
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="enabled" value='1' type="checkbox" {if $feed->enabled}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Дополнительные настройки*}
    {$switch_checkboxes = {get_design_block block="okay_cms__feeds__feed__switch_checkboxes"}}
    {if !empty($switch_checkboxes)}
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="boxed fn_toggle_wrap ">
                    <div class="heading_box">
                        {$btr->general_additional_settings|escape}
                        <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                            <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="toggle_body_wrap on fn_card">
                        <div class="activity_of_switch activity_of_switch--box_settings">
                            {$switch_checkboxes}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {$block = {get_design_block block="okay_cms__feeds__feed__custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}

    <div class="tabs mt-1">
        <div class="heading_tabs">
            <div class="tab_navigation">
                <a href="#tab_entities" class="heading_box tab_navigation_link">{$btr->okay_cms__feeds__feed_tab_entities|escape}</a>
                <a href="#tab_settings" class="heading_box tab_navigation_link">{$btr->okay_cms__feeds__feed_tab_settings|escape}</a>
                <a href="#tab_feature_mappings" class="heading_box tab_navigation_link">{$btr->okay_cms__feeds__feed_tab_feature_mappings|escape}</a>
                <a href="#tab_category_mappings" class="heading_box tab_navigation_link">{$btr->okay_cms__feeds__feed_tab_category_mappings|escape}</a>
            </div>
        </div>
        <div class="tab_container">
            <div id="tab_entities" class="tab">
                {include file='./feed_tabs/entities.tpl'}
            </div>
            <div id="tab_settings" class="tab fn_settings_container">
                {include file='./feed_tabs/settings.tpl'}
            </div>
            <div id="tab_feature_mappings" class="tab">
                {include file='./feed_tabs/feature_mappings.tpl'}
            </div>
            <div id="tab_category_mappings" class="tab">
                {include file='./feed_tabs/category_mappings.tpl'}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <button type="submit" class="btn btn_small btn_blue float-md-right mb-1">
                {include file='svg_icon.tpl' svgId='checked'}
                <span>{$btr->general_apply|escape}</span>
            </button>
        </div>
    </div>
</form>

<div class="fn_new_settings_container hidden">
    {foreach $settings_templates as $preset_name => $st}
        <div class="fn_settings fn_new_settings" data-preset_name="{$preset_name}">
            {$st}
        </div>
    {/foreach}
</div>