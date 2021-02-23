{* Title *}
{$meta_title=$btr->features_features scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                {$btr->features_features|escape} - {$features_count}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=FeatureAdmin return=$smarty.server.REQUEST_URI page=null limit=null}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->features_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="FeaturesAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->features_search|escape}" type="text" value="{$keyword|escape}" >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn_blue"><i class="fa fa-search"></i> <span class="hidden-md-down"></span></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

{*Блок фильтров*}
<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="fn_step-1 fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->general_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="boxed_sorting toggle_body_wrap off fn_card">
                    <div class="row">
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <select id="id_categories" name="categories_filter" title="{$btr->general_category_filter|escape}" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="location = this.value;">
                                <option value="{url keyword=null brand_id=null page=null limit=null category_id=null}" {if !$category}selected{/if}>{$btr->general_all_categories|escape}</option>
                                {function name=category_select level=0}
                                    {foreach $categories as $c}
                                        <option value='{url keyword=null category_id=$c->id}' {if $category->id == $c->id}selected{/if}>
                                            {section sp $level}-{/section}{$c->name|escape}
                                        </option>
                                        {category_select categories=$c->subcategories level=$level+1}
                                    {/foreach}
                                {/function}
                                {category_select categories=$categories_tree}
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm 12">
                            <select onchange="location = this.value;" class="selectpicker form-control">
                                <option value="{url limit=5}" {if $current_limit == 5}selected{/if}>{$btr->general_show_by|escape} 5</option>
                                <option value="{url limit=10}" {if $current_limit == 10}selected{/if}>{$btr->general_show_by|escape} 10</option>
                                <option value="{url limit=25}" {if $current_limit == 25}selected{/if}>{$btr->general_show_by|escape} 25</option>
                                <option value="{url limit=50}" {if $current_limit == 50}selected{/if}>{$btr->general_show_by|escape} 50</option>
                                <option value="{url limit=100}" {if $current_limit == 100}selected=""{/if}>{$btr->general_show_by|escape} 100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {$block = {get_design_block block="features_custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}

    {*Главная форма страницы*}
    {if $features}
        <form method="post" class="fn_form_list fn_fast_button">
            <input type="hidden" name="session_id" value="{$smarty.session.id}"/>

            <div class="okay_list products_list fn_sort_list">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_heading okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_features_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_features_tag">{$btr->general_categories|escape}</div>
                    <div class="okay_list_heading okay_list_setting okay_list_features_setting"></div>
                    <div class="okay_list_heading okay_list_url_status">{$btr->feature_url_in_product_short|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->features_in_filter|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>
                {*Параметры элемента*}
                <div class="okay_list_body features_wrap sort_extended">
                {foreach $features as $feature}
                    <div class="fn_step-2 fn_row okay_list_body_item fn_sort_item">
                        <div class="okay_list_row ">
                            <input type="hidden" name="positions[{$feature->id}]" value="{$feature->position}" />

                            <div class="okay_list_boding okay_list_drag move_zone">
                                {include file='svg_icon.tpl' svgId='drag_vertical'}
                            </div>

                            <div class="okay_list_boding okay_list_check">
                                <input class="hidden_check" type="checkbox" id="id_{$feature->id}" name="check[]" value="{$feature->id}" />
                                <label class="okay_ckeckbox" for="id_{$feature->id}"></label>
                            </div>

                            <div class="okay_list_boding okay_list_features_name">
                                <a class="link" href="{url controller=FeatureAdmin id=$feature->id return=$smarty.server.REQUEST_URI page=null limit=null}">
                                    {$feature->name|escape}
                                </a>

                                {get_design_block block="features_list_name"}
                            </div>

                            <div class="okay_list_boding okay_list_features_tag">
                                <div class="wrap_tags">
                                {if $feature->features_categories}
                                    {foreach $feature->features_categories as $feature_cat}
                                        {if $feature_cat@iteration <= 12 && isset($categories[$feature_cat])}
                                           <span class="tag tag-info">{$categories[$feature_cat]->name|escape}</span>
                                        {/if}
                                    {/foreach}
                                {/if}
                                </div>
                            </div>
                            <div class="okay_list_boding okay_list_setting okay_list_features_setting"></div>
                            <div class="okay_list_boding okay_list_url_status">
                                {*url_in_product*}
                                <label class="switch switch-default">
                                    <input class="switch-input fn_ajax_action {if $feature->url_in_product}fn_active_class{/if}" data-controller="feature" data-action="url_in_product" data-id="{$feature->id}" name="url_in_product" value="1" type="checkbox"  {if $feature->url_in_product}checked=""{/if}/>
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                            <div class="okay_list_boding okay_list_status">
                                {*visible*}
                                <label class="switch switch-default">
                                    <input class="switch-input fn_ajax_action {if $feature->in_filter}fn_active_class{/if}" data-controller="feature" data-action="in_filter" data-id="{$feature->id}" name="in_filter" value="1" type="checkbox"  {if $feature->in_filter}checked=""{/if}/>
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                            <div class="okay_list_boding okay_list_close">
                                {*delete*}
                                <button data-hint="{$btr->features_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                    {include file='svg_icon.tpl' svgId='trash'}
                                </button>
                            </div>
                        </div>
                    </div>
                {/foreach}
                </div>

                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        <div class="okay_list_heading okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker features_action">
                                <option value="set_in_filter">{$btr->features_in_filter|escape}</option>
                                <option value="unset_in_filter">{$btr->features_not_in_filter|escape}</option>
                                <option value="delete">{$btr->general_delete|escape}</option>
                                {if $pages_count>1}
                                    <option value="move_to_page">{$btr->products_move_to_page|escape}</option>
                                {/if}
                            </select>
                        </div>
                        <div class="fn_additional_params">
                            <div class="fn_move_to_page col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                <select name="target_page" class="selectpicker">
                                    {section target_page $pages_count}
                                        <option value="{$smarty.section.target_page.index+1}">{$smarty.section.target_page.index+1}</option>
                                    {/section}
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                         {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->features_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_features'}

{literal}
    <script>
        $(function() {
            $(document).on('change', '.fn_action_block select.features_action', function () {
                var elem = $(this).find('option:selected').val();
                $('.fn_hide_block').addClass('hidden');
                if ($('.fn_' + elem).size() > 0) {
                    $('.fn_' + elem).removeClass('hidden');
                }
            });
        });
    </script>
{/literal}
