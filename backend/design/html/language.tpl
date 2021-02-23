{if $language->id}
    {$meta_title = $language->name|escape scope=global}
{else}
    {$meta_title = $btr->language_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        {if !$language->id}
            <div class="heading_page">{$btr->language_add|escape}</div>
        {else}
            <div class="heading_page">{$language->name|escape}</div>
        {/if}
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
                        {$btr->language_added|escape}
                        {elseif $message_success == 'updated'}
                        {$btr->language_updated|escape}
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
                        {if $message_error == 'label_empty'}
                        {$btr->language_empty_label|escape}
                        {elseif $message_error == 'label_exists'}
                        {$btr->language_used|escape}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="id" value="{$language->id|escape}"/>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap clearfix">
                <div class="heading_box">
                    {if !$language->id}
                        {$btr->general_languages|escape}
                    {else}
                        {$btr->language_translations|escape}
                    {/if}
                </div>
                <div class="float-md-right">
                    <div class="activity_of_switch">
                        <div class="activity_of_switch_item">
                            <div class="okay_switch clearfix">
                                <label class="switch_label text_500 font_14 opensans text_grey mx-h">{$btr->general_enable|escape}</label>
                                <label class="switch switch-default">
                                    <input class="switch-input" name="enabled" value='1' type="checkbox" {if $language->enabled}checked=""{/if}/>
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        {if !$language->id}
                            <div class="col-md-6">
                                <div class="heading_label">{$btr->language_select|escape} </div>
                                <div class="">

                                   <select id="countries_select"  name="lang" size="1">
                                        {foreach $lang_list as $lang}
                                            <option value='{$lang->label}' data-image="{if is_file("{$config->lang_images_dir}{$lang->label}.png")}{("{$lang->label}.png")|resize:23:23:false:$config->lang_resized_dir}"{/if}" data-imagecss="flag ad" data-title="{$lang->name|escape}">{$lang->name|escape} [{$lang->label|escape}]</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        {else}
                            <div class="col-md-12">
                                <div class="row">
                                    {foreach $languages as $l}
                                        <div class="col-lg-4 col-md-4 col-sm-6 mb-1">
                                            <div class="heading_label mb-h">
                                                {if is_file("{$config->lang_images_dir}{$l->label}.png")}
                                                    <span class="wrap_flag">
                                                        <img src="{("{$l->label}.png")|resize:55:55:false:$config->lang_resized_dir}" />
                                                    </span>
                                                {/if}
                                                {$l->name|escape}
                                            </div>
                                            <div class="">
                                                <input type="text" name="other_languages_names[{$l->id}]" value="{$language->names[{$l->id}]}" class="form-control"/>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        {/if}

                        <div class="{if !$language->id}col-lg-6 col-md-6 mt-2  {else} col-lg-12 col-md-12 mt-1{/if}">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
