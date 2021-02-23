{* Title *}
{$meta_title=$btr->translations_translate scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->translations_translate|escape}{if $settings->admin_theme} {$btr->theme_theme} {$settings->admin_theme|escape}{/if}
            </div>
            {if !$locked_theme}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" href="{url controller=TranslationAdmin return=$smarty.server.REQUEST_URI}">
                        {include file='svg_icon.tpl' svgId='plus'}
                        <span>{$btr->translations_add|escape}</span>
                    </a>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_description|escape}</div>
                <p>{$btr->general_translation_attention|escape}</p>
            </div>
        </div>
    </div>
</div>

{if $locked_theme}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">{$btr->general_protected|escape}</div>
                </div>
            </div>
        </div>
    </div>
{/if}

{$block = {get_design_block block="translations_custom_block"}}
{if $block}
    <div class="row custom_block">
        {$block}
    </div>
{/if}

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    <form class="fn_form_list" method="post">
        <input type="hidden" name="session_id" value="{$smarty.session.id}">

        <div class="translation_wrap okay_list products_list fn_sort_list">
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_check">
                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                    <label class="okay_ckeckbox" for="check_all_1"></label>
                </div>
                <div class="okay_list_heading okay_list_translations_num">
                    <span>№</span>
                </div>
                <div class="okay_list_heading okay_list_translations_name">
                    <a {if $sort=='translation' || $sort=='translation_desc'} class="selected" {/if} href="{if $sort=='translation'}{url sort=translation_desc}{else}{url sort=translation}{/if}">
                    {$btr->translations_translation|escape} {include file='svg_icon.tpl' svgId='sorting'}
                    </a>
                </div>
                <div class="okay_list_heading okay_list_translations_variable">
                    <a {if $sort=='label' || $sort=='label_desc' || !$sort} class="selected" {/if}href="{if $sort=='label' || !$sort}{url sort=label_desc}{else}{url sort=null}{/if}">
                    {$btr->translations_var|escape} {include file='svg_icon.tpl' svgId='sorting'}
                    </a>
                </div>
                <div class="okay_list_heading okay_list_close"></div>
            </div>

            {*Параметры элемента*}
            <div class="okay_list_body">
                {foreach $translations as $label=>$value}
                    <div class="fn_row okay_list_body_item fn_sort_item"{if !$translations_template[$label]} title="{$btr->translations_system_translation}"{/if}>
                        <div class="okay_list_row ">
                            <div class="okay_list_boding okay_list_check">
                                <input class="hidden_check" type="checkbox" id="{$label}" name="check[]" value="{$label}" />
                                <label class="okay_ckeckbox" for="{$label}"></label>
                            </div>
                            <div class="okay_list_heading okay_list_translations_num">№ {$value@iteration}</div>
                            <div class="okay_list_boding okay_list_translations_name">
                                <a href="{url controller=TranslationAdmin id=$label return=$smarty.server.REQUEST_URI}">{$value|escape}</a>
                            </div>
                            <div class="okay_list_boding  okay_list_translations_variable">
                                 <a href="{url controller=TranslationAdmin id=$label return=$smarty.server.REQUEST_URI}">{$label|escape}</a>
                            </div>
                            <div class="okay_list_boding okay_list_close">
                                {if !$locked_theme && $translations_template[$label]}
                                    <button data-hint="{$btr->general_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                        {include file='svg_icon.tpl' svgId='trash'}
                                    </button>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>

            {if !$locked_theme}
                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker">
                                <option value="delete">{$btr->general_delete|escape}</option>
                            </select>
                        </div>
                    </div>
                    <button id="apply_action" type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            {/if}
        </div>
    </form>
</div>
