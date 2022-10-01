{if $feed->id}
    {$meta_title = $feed->name scope=global}
{else}
    {$meta_title = $btr->okay_cms__feeds__feed__new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$feed->id}
                    {$btr->okay_cms__feeds__feed__add|escape}
                {else}
                    {$feed->name|escape}
                {/if}
            </div>
            {if $feed->id}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="{url_generator route="OkayCMS.Feeds.Feed" url=$feed->url absolute=1}">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                        <span>{$btr->okay_cms__feeds__feeds__open|escape}</span>
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
                            {$btr->okay_cms__feeds__feed__added|escape}
                        {elseif $message_success == 'updated'}
                            {$btr->okay_cms__feeds__feed__updated|escape}
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

<form method="post" class="fn_fast_button" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />

    <div class="row">
        <div class="col-xs-12 ">
            <div class="boxed match_matchHeight_true">
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="">
                            <div class="heading_label heading_label--required">
                                <span>{$btr->general_name|escape}</span>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="name" type="text" value="{$feed->name|escape}"/>
                                <input name="id" type="hidden" value="{$feed->id|escape}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-lg-6">
                                <div class="mb-1 mt-2 mt-2-md-down">
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
                                        <span>{$btr->okay_cms__feeds__feed__preset}</span>
                                    </div>
                                    <select class="selectpicker form-control mb-1 fn_preset_select" name="preset">
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
                            <div class="activity_of_switch_item">
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

    <div class="tabs">
        <div class="heading_tabs">
            <div class="tab_navigation tab_navigation--round">
                <a href="#tab_entities" class="heading_box tab_navigation_link">
                    {include file='svg_icon.tpl' svgId='feed_product'}
                    {$btr->okay_cms__feeds__feed__entities__tab|escape}
                </a>
                <a href="#tab_settings" class="heading_box tab_navigation_link">
                    {include file='svg_icon.tpl' svgId='feed_settings'}
                    {$btr->okay_cms__feeds__feed__settings__tab|escape}
                </a>
                <a href="#tab_features_settings" class="heading_box tab_navigation_link hidden-xs-down">
                    {include file='svg_icon.tpl' svgId='feed_features'}
                    {$btr->okay_cms__feeds__feed__features_settings__tab|escape}
                </a>
                <a href="#tab_categories_settings" class="heading_box tab_navigation_link hidden-xs-down">
                    {include file='svg_icon.tpl' svgId='feed_category'}
                    {$btr->okay_cms__feeds__feed__categories_settings__tab|escape}
                </a>
            </div>
        </div>
        <div class="tab_container tab_container--h-auto">
            <div id="tab_entities" class="tab">
                {include file='./feed_tabs/entities.tpl'}
            </div>
            <div id="tab_settings" class="tab fn_settings_container">
                {include file='./feed_tabs/settings.tpl'}
            </div>
            <div id="tab_features_settings" class="tab">
                {include file='./feed_tabs/features_settings.tpl'}
            </div>
            <div id="tab_categories_settings" class="tab">
                {include file='./feed_tabs/categories_settings.tpl'}
            </div>
        </div>
        <div class="boxed">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <button type="submit" class="btn btn_small btn_blue float-md-right">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
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


{* styling module feeds *}
<style>
.feed_select_type{
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 220px;
    -ms-flex: 0 0 220px;
    flex: 0 0 220px;
    max-width: 220px;
}
.feed_condition_item{
    border: 1px solid rgb(238, 238, 238);
    padding-top: 1rem;
    margin: 0;
    margin-top: 1rem;
}
.feed_condition_delete{
    position: absolute;
    top: -10px;
    right: 5px;
    padding: 5px;
    color: #8c8c8c !important;
}
.feed_condition_delete svg{
    height: 14px;
    width: 14px;
}
.ok_feed_condition_list { 
    margin-top: 15px;
    border: none;
}
.ok_feed_condition_list .okay_list_body_item {
    border-top: 1px solid #f2f2f2;
    border-bottom: none;
    min-height: 80px;
}
.f_col-lg {
    -webkit-flex-basis: 0;
    -ms-flex-preferred-size: 0;
    flex-basis: 0;
    -webkit-box-flex: 1;
    -webkit-flex-grow: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    max-width: 100%;
}
.okay_list .okay_list_feed_categories_settings_name{
    width: calc(100% - 500px);
    position: relative;
    text-align: left;
}
.okay_list .subcategories_level_1 .okay_list_feed_categories_settings_name{
    width: calc(100% - 530px);
}
.okay_list .subcategories_level_2 .okay_list_feed_categories_settings_name{
    width: calc(100% - 560px);
}
.okay_list_feed_features_settings_settings,
.okay_list_feed_categories_settings_settings {
    width: 350px;
    text-align: left;
}
.okay_list_feed_num{
    width: 60px;
}
.okay_list .okay_list_feed_features_settings_name{
    width: calc(100% - 430px);
    position: relative;
    text-align: left;
}
.pr-0.pr-0--feed{
    padding-right: 0 !important;
}
.settings_added_design_elements{
    text-align: left;
}
.bs-actionsbox .btn-group button {
    width: calc(50% - 6px);
}
.bs-deselect-all,
.bs-select-all{
    text-align: center;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
}
.bs-select-all{
    background: rgb(172, 208, 255);
}
.bs-deselect-all{
    background: rgb(255, 172, 172);
}
.bs-select-all:hover{
    background: rgb(143, 192, 255);
}
.bs-deselect-all:hover{
    background: rgb(255, 136, 136);
}

@media (max-width: 991px) {
    .mt-2-md-down{
        margin-top: 0rem !important;
    }
}
@media (max-width: 767px) {
    .feed_condition_delete{
        top: -15px;
        right: 5px;
    }
    #tab_entities .box_btn_heading{
        display:block;
        margin: 10px 0 0!important;
    }
    .tab_navigation--round .tab_navigation_link {
        margin-right: 0px;
    }
    .feed_select_type {
        -webkit-box-flex: 0;
        -webkit-flex: 0 0 100%;
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 100%;
    }
    .okay_list_feed_features_settings_settings,
    .okay_list_feed_categories_settings_settings {
        width: 250px;
    }
    .okay_list .okay_list_feed_features_settings_name{
        width: calc(100% - 310px);
    }
    .okay_list .okay_list_feed_categories_settings_name{
        width: calc(100% - 300px);
    }
    .okay_list .subcategories_level_1 .okay_list_feed_categories_settings_name{
        width: calc(100% - 330px);
    }
    .okay_list .subcategories_level_2 .okay_list_feed_categories_settings_name{
        width: calc(100% - 360px);
    }
}
</style>
