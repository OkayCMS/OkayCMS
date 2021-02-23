{* Title *}
{$meta_title=$btr->blog_blog scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                {if $keyword && $posts_count}
                   {$btr->blog_blog|escape} - {$posts_count}
                {elseif $posts_count}
                    {$btr->blog_blog|escape} - {$posts_count}
                {/if}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=PostAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->blog_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="BlogAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->blog_search|escape}" type="text" value="{$keyword|escape}" >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn_blue"><i class="fa fa-search"></i> <span class="hidden-md-down"></span></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    <div class="row">
        {*Блок фильтров*}
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_toggle_wrap ">
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
                            <option value="{url keyword=null brand_id=null page=null limit=null category_id=null}" {if !$category_id}selected{/if}>{$btr->general_all_categories|escape}</option>
                            <option value="{url keyword=null brand_id=null page=null limit=null category_id=-1}" {if $category_id==-1}selected{/if}>{$btr->products_without_category|escape}</option>
                            {function name=category_select level=0}
                                {foreach $categories as $c}
                                    <option value='{url keyword=null brand_id=null page=null limit=null category_id=$c->id}' {if $category_id == $c->id}selected{/if}>
                                        {section sp $level}- {/section}{$c->name|escape}
                                    </option>
                                    {category_select categories=$c->subcategories level=$level+1}
                                {/foreach}
                            {/function}
                            {category_select categories=$categories}
                        </select>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    {$block = {get_design_block block="blog_custom_block"}}
    {if !empty($block)}
        <div class="fn_toggle_wrap" style="height: 40px; margin-bottom: 5px;">
            {$block}
        </div>
    {/if}

    {*Главная форма страницы*}
    {if $posts}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list fn_fast_button" method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                    <div class="post_wrap okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                            <div class="okay_list_heading okay_list_blog_name">{$btr->blog_name|escape}</div>
                            <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading okay_list_setting okay_list_blog_setting">{$btr->general_activities|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {foreach $posts as $post}
                                <div class="fn_step-1 fn_row okay_list_body_item">
                                    <div class="okay_list_row">

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$post->id}" name="check[]" value="{$post->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$post->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_photo">
                                            {if $post->image}
                                                <a href="{url controller=PostAdmin id=$post->id return=$smarty.server.REQUEST_URI}">
                                                    <img src="{$post->image|escape|resize:55:55:false:$config->resized_blog_dir}"/>
                                                </a>
                                            {else}
                                                <img height="55" width="55" src="design/images/no_image.png"/>
                                            {/if}
                                        </div>

                                        <div class="okay_list_boding okay_list_blog_name">
                                            <a class="link" href="{url controller=PostAdmin id=$post->id return=$smarty.server.REQUEST_URI}">{$post->name|escape}</a>
                                            <span class="text_grey">{$post->date|date}</span>

                                            {get_design_block block="blog_post_name" vars=['post'=>$post]}
                                        </div>

                                        <div class="okay_list_boding okay_list_status">
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $post->visible}fn_active_class{/if}" data-controller="blog" data-action="visible" data-id="{$post->id}" name="visible" value="1" type="checkbox"  {if $post->visible}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_setting okay_list_blog_setting">
                                            {*open*}
                                            <a href="{url_generator route='post' url=$post->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='eye'}
                                            </a>

                                            {get_design_block block="blog_icon" vars=['post'=>$post]}
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->blog_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker form-control">
                                        <option value="enable">{$btr->general_do_enable|escape}</option>
                                        <option value="disable">{$btr->general_do_disable|escape}</option>
                                        <option value="delete">{$btr->general_delete|escape}</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn_small btn_blue">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                    {include file='pagination.tpl'}
                </div>
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->blog_no_post|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_blog'}