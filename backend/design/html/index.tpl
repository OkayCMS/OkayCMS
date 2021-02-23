<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
    <META HTTP-EQUIV="Expires" CONTENT="-1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>{$meta_title|escape}</title>

    {literal}
    <script>
        /* Initializing clipboard */
        sclipboard();
        function sclipboard() {
            const links = document.querySelectorAll('.fn_clipboard');
            const cls = {
                copied: 'is-copied',
                hover: 'hint-anim'
            };

            const copyToClipboard = str => {
                const el = document.createElement('input');
                str.dataset.copyString ? el.value = str.dataset.copyString : el.value = str.text;
                el.setAttribute('readonly', '');
                el.style.position = 'absolute';
                el.style.opacity = 0;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            };
            const clickInteraction = e => {
                e.preventDefault();
                copyToClipboard(e.target);
                e.target.classList.add(cls.copied);
                setTimeout(() => e.target.classList.remove(cls.copied), 1000);
                setTimeout(() => e.target.classList.remove(cls.hover), 700);
            };
            Array.from(links).forEach(link => {
                link.addEventListener('click', e => clickInteraction(e));
                link.addEventListener('keypress', e => {
                    if (e.keyCode === 13) clickInteraction(e);
                });
                link.addEventListener('mouseover', e => e.target.classList.add(cls.hover));
                link.addEventListener('mouseleave', e => {
                    if (!e.target.classList.contains(cls.copied)) {
                        e.target.classList.remove(cls.hover);
                    }
                });
            });
        };

        var okay = {};
        okay.router = {};
        {/literal}
        {if $front_routes}
            {foreach $front_routes as $name=>$route}
                okay.router['{$name}'] = '{url_generator route=$name absolute=1}';
            {/foreach}
        {/if}
        {literal}
    </script>
    {/literal}

    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300i,700|Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet" type="text/css">
    
    {$ok_head}
    
    <link rel="icon" href="design/images/favicon.png" type="image/x-icon" />

    {if in_array($smarty.get.controller, array("OrdersAdmin", "PostAdmin", "ReportStatsAdmin", "CouponsAdmin", "CategoryStatsAdmin"))}
        {js file="jquery/datepicker/jquery.ui.datepicker-{$manager->lang}.js" admin=true}
        {js file="jquery/datepicker/jquery.datepicker.extension.range.min.js" admin=true}
    {/if}
    
    <!-- Google Tag Manager -->
    {if $settings->gather_enabled}
        {literal}
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','GTM-P6T2LJP');
        </script>
        {/literal}
    {/if}
    <!-- End Google Tag Manager -->

</head>
<body class="navbar-fixed {if $manager->menu_status && $is_mobile === false && $is_tablet === false}menu-pin{/if}">
    <!-- Google Tag Manager (noscript) -->
    {if $settings->gather_enabled}
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P6T2LJP" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    {/if}
    <!-- End Google Tag Manager (noscript) -->
    <header class="navbar">
        <div class="container-fluid">
            <div id="mobile_menu" class="fn_mobile_menu hidden-xl-up  text_white">
                {include file='svg_icon.tpl' svgId='mobile_menu'}
            </div>
            <div class="admin_switches">
                <div class="box_adswitch">
                    <a class="btn_admin" target="_blank" href="{url_generator route="main" absolute=1}">
                    {include file='svg_icon.tpl' svgId='icon_desktop'}
                    <span class="">{$btr->index_go_to_site|escape}</span>
                    </a>
                </div>
            </div>
            <div class="admin_switches admin_switches_two hidden-sm-down">
                {include file="video_help.tpl"}
            </div>
            <div class="admin_switches admin_switches_three">
                <div class="box_adswitch">
                    {if !empty($has_new_version)}
                        <a class="btn_admin btn_version_old hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->index_btn_version_old|escape} {$has_new_version.version|escape}" {if $has_new_version.info_href}target="_blank" href="{$has_new_version.info_href|escape}"{else}href="javascript:;"{/if}>
                            {include file='svg_icon.tpl' svgId='no_icon'}
                            <span class="">Version {$config->version}</span>
                        </a>
                    {else}
                        <div class="btn_admin btn_version_new hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->index_btn_version_new|escape}">
                            {include file='svg_icon.tpl' svgId='yes_icon'}
                            <span class="">Version {$config->version}</span>
                        </div>
                    {/if}
                </div>
            </div>
            <div id="mobile_menu_right" class="fn_mobile_menu_right hidden-md-up  text_white float-xs-right">
                {include file='svg_icon.tpl' svgId='mobile_menu2'}
            </div>
            <div id="quickview" class="fn_quickview">
                <div class="sidebar_header hidden-md-up">
                        <span class="fn_switch_quickview menu_switch">
                            <span class="menu_hamburger"></span>
                        </span>
                    <a href="index.php?controller={$manager_main_controller}" class="logo_box">
                        <img src="design/images/logo_title.png" alt="OkayCMS"/>
                    </a>
                </div>
                <div class="admin_exit hidden-sm-down hint-bottom-right-t-info-s-small-mobile  hint-anim" data-hint="{$btr->index_exit|escape}">
                    <a href="{$rootUrl}?logout">
                        {*<span class="hidden-lg-down">{$btr->index_exit|escape}</span>*}
                        {include file='svg_icon.tpl' svgId='exit'}
                    </a>
                </div>
                <div class="admin_name hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$manager->login|escape}">
                    <a href="index.php?controller=ManagerAdmin&id={$manager->id}">
                        {*<span class="">{$manager->login|escape}</span>*}
                        {include file='svg_icon.tpl' svgId='user2_icon'}
                    </a>
                </div>
                {*Счетчики уведомлений*}
                <div class="admin_notification">
                    <div class="notification_inner">
                            <span class="notification_title" href="">
                                {*<span class="quickview_hidden">{$btr->index_notifications|escape}</span>*}
                                {include file='svg_icon.tpl' svgId='notify'}
                                {if $all_counter}
                                    <span class="counter">{$all_counter}</span>
                                {/if}
                            </span>
                        <div class="notification_toggle">
                            {if $new_orders_counter > 0}
                            <div class="notif_item">
                                <a href="index.php?controller=OrdersAdmin" class="l_notif">
                                    <span class="notif_icon boxed_notify">
                                        {include file='svg_icon.tpl' svgId='left_orders'}
                                    </span>
                                    <span class="notif_title">{$btr->general_orders|escape}</span>
                                </a>
                                <span class="notif_count">{$new_orders_counter}</span>
                            </div>
                            {/if}
                            {if $new_comments_counter > 0}
                            <div class="notif_item">
                                <a href="index.php?controller=CommentsAdmin" class="l_notif">
                                    <span class="notif_icon boxed_warning">
                                        {include file='svg_icon.tpl' svgId='left_comments'}
                                    </span>
                                    <span class="notif_title">{$btr->general_comments|escape}</span>
                                </a>
                                <span class="notif_count">{$new_comments_counter}</span>
                            </div>
                            {/if}
                            {if $new_feedbacks_counter > 0}
                            <div class="notif_item">
                                <a href="index.php?controller=FeedbacksAdmin" class="l_notif">
                                    <span class="notif_icon boxed_yellow">
                                        {include file='svg_icon.tpl' svgId='email'}
                                    </span>
                                    <span class="notif_title">{$btr->general_feedback|escape}</span>
                                </a>
                                <span class="notif_count">{$new_feedbacks_counter}</span>
                            </div>
                            {/if}
                            {if $new_callbacks_counter > 0}
                            <div class="notif_item">
                                <a href="index.php?controller=CallbacksAdmin" class="l_notif">
                                    <span class="notif_icon boxed_attention">
                                        {include file='svg_icon.tpl' svgId='phone'}
                                    </span>
                                    <span class="notif_title">{$btr->general_callback|escape}</span>
                                </a>
                                <span class="notif_count">{$new_callbacks_counter}</span>
                            </div>
                            {/if}
                            {if !$new_orders_counter > 0 && !$new_comments_counter > 0 && !$new_feedbacks_counter > 0 && !$new_callbacks_counter > 0}
                            <div class="notif_item">
                                <span class="notif_title">{$btr->index_no_notification|escape}</span>
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
                {*Техподдержка*}
                <div class="admin_techsupport">
                    <div class="techsupport_inner">
                        <a {if $support_info->public_key} data-hint="{$support_info->balance|balance}"{else} data-hint="Not active" {/if}  class="hint-bottom-middle-t-info-s-small-mobile  hint-anim"  href="index.php?controller=SupportAdmin">
                            <span class="quickview_hidden">{$btr->index_support|escape}</span>
                            {include file='svg_icon.tpl' svgId='techsupport'}
                            {if $support_info->public_key}
                            <span class="counter">{$support_info->new_messages}</span>
                            {/if}
                        </a>
                        <div class="techsupport_toggle hidden-md-up">
                            {if $support_info->public_key}
                            <span>{$support_info->balance|balance}</span>
                            {else}
                            <span>Not active</span>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="admin_languages" >
                    <div class="languages_inner">
                        <span class="languages_title hidden-md-up">{$btr->general_languages|escape}</span>
                        {include file="include_languages.tpl"}
                    </div>
                </div>
                <div class="admin_exit hidden-md-up">
                    <a href="{$rootUrl}?logout">
                        <span class="">{$btr->index_exit|escape}</span>
                        {include file='svg_icon.tpl' svgId='exit'}
                    </a>
                </div>
            </div>
        </div>
    </header>
    <nav id="admin_catalog" class="fn_left_menu">
        <div id="mob_menu"></div>
        <div class="sidebar_header">
            <a href="javascript:;" id="fix_logo" class="hidden-lg-down"></a>

            <a href="index.php?controller={$manager_main_controller}" class="logo_box">
                <img src="design/images/logo_title.png" alt="OkayCMS"/>
            </a>
            {if $is_mobile === false && $is_tablet === false}
                <span class="fn_switch_menu menu_switch fn_ajax_action {if $manager->menu_status}fn_active_class{/if} hint-left-middle-t-white-s-small-mobile  hint-anim" data-controller="managers" data-action="menu_status" data-id="{$manager->id}" data-hint="{$btr->catalog_fixation}">
                    <span class="menu_hamburger"></span>
                </span>
            {else}
                <span class="fn_switch_menu menu_switch" data-controller="managers" data-action="menu_status" data-id="{$manager->id}">
                    <span class="menu_hamburger"></span>
                </span>
            {/if}
        </div>
        {*Меню админ. панели*}
        <div class="sidebar sidebar-menu">
            <div class="scrollbar-inner menu_items">
                <div>
                    <form class="fn_manager_menu">
                        <input type="hidden" name="object" value="managers" />
                        <input type="hidden" name="session_id" value="{$smarty.session.id}" />
                        <input type="hidden" name="id" value="{$manager->id}" />
                        <ul id="fn_sort_menu_section" class="menu_items">
                            {foreach $left_menu as $section=>$items}
                                <li class="{if isset($items.$menu_selected)}open active{/if} {if $items|count > 1} fn_item_sub_switch nav-dropdown{/if}">
                                    {if $items|count == 1}
                                        <input type="hidden" value="{$items|reset}" name="manager_menu[{$section|escape}][{$items|key}]" />
                                    {/if}

                                    {if $config->dev_mode}
                                        <div class="fn_backend_menu_section" data-section_name="{$section}">{$section}</div>
                                    {/if}

                                    <a class="fn_learning_{$section} nav-link {if $items|count > 1}fn_item_switch nav-dropdown-toggle{/if}" href="{if $items|count > 1}javascript:;{else}index.php?controller={$items|reset|reset}{/if}">
                                        <span class="{$section} title">{$btr->getTranslation({$section})}</span>
                                        <span class="icon-thumbnail">
                                            {if !empty($additional_section_icons[$section])}
                                                {if $additional_section_icons[$section]['type'] === 'file'}
                                                    <img src="{$rootUrl}/{$additional_section_icons.$section.data}">
                                                {else}
                                                    {$additional_section_icons.$section.data}
                                                {/if}
                                            {else}
                                                {$svg_icon = {include file='svg_icon.tpl' svgId=$section}}
                                                {if $svg_icon}
                                                    {$svg_icon}
                                                {else}
                                                    {$translation = {$btr->getTranslation({$section})}}
                                                    <span class="manager_menu_section_icon">{$translation|first_letter}</span>
                                                {/if}
                                            {/if}
                                        </span>
                                        {if $items|count >1}
                                            <span class="arrow"></span>
                                        {/if}
                                        {if isset($menu_counters[$section]) && !empty($menu_counters[$section])}
                                            <span class="menu_counter">
                                                {$menu_counters[$section]}
                                            </span>
                                        {/if}
                                    </a>
                                    {if $items|count > 1}
                                        <ul class="fn_submenu_toggle submenu fn_sort_menu_item">
                                            {foreach $items as $title=>$item}
                                                <li class="{if in_array($controller_selected, $item.controllers_block)}active{/if}">
                                                    <input type="hidden" name="manager_menu[{$section|escape}][{$title|escape}]" value="{$item.controller|escape}" />
                                                    <a class="fn_learning_{$item.controller} nav-link" href="index.php?controller={$item.controller}{if !empty($item.method)}@{$item.method}{/if}">
                                                        <span class="icon-thumbnail">
                                                            {if (isset($menu_counters[$title]) && !empty($menu_counters[$title])) || $config->dev_mode}
                                                                <span class="menu_counter">
                                                                    {if $config->dev_mode}
                                                                        <div class="fn_backend_menu_section menu_counter_name" data-section_name="{$title}">{$title}</div>
                                                                    {/if}
                                                                    {$menu_counters[$title]}
                                                                </span>
                                                            {/if}
                                                            {$btr->getTranslation({$title})|first_letter}
                                                        </span>
                                                        <span class="{$title} title">{$btr->getTranslation({$title})}</span>
                                                    </a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    {/if}
                                </li>
                            {/foreach}
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    {*Верхняя шапка*}
    <div class="page-container">
        <a href='{url_generator route="main" absolute=1}' class='admin_bookmark'></a>

        <div class="main">
            <div class="container-fluid">
                <div class="min_content_fix">
                    {if $content}
                        {$content}
                    {else}
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mt-1">
                                <div class="boxed boxed_warning">
                                    <div class="heading_box">
                                        {$btr->general_no_permission}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                <footer id="footer" class="">
                    <div class="col-md-12 font_12 text_white">
                        <a href="https://okay-cms.com">OkayCMS </a>
                        &copy; {$smarty.now|date_format:"%Y"} 
                        v.{$config->version} | {$btr->index_logged|escape} 
                        <a href="index.php?controller=ManagerAdmin&id={$manager->id}">{$manager->login|escape}</a>
                        (<a href="{$rootUrl}?logout">{$btr->index_exit|escape}</a>)
                        <div class="float-md-right">
                            <a href='index.php?controller=LicenseAdmin' class="text_white">{$btr->license_text|escape} </a>
                            ,
                            {if $support_info->public_key}
                                <a class="text_success" href="index.php?controller=SupportAdmin">{$btr->index_support_active|escape} ({$support_info->new_messages})</a>
                            {else}
                                <a href="index.php?controller=SupportAdmin">
                                    <span class="text_warning">{$btr->index_support_not_active|escape}</span>
                                </a>
                            {/if}
                        </div>
                    </div>
                </footer>
             </div>
            {*Быстрое сохранение*}
            <div class="fn_fast_save">
                <div class="fn_fast_action_block fn_action_block">
                    <div class="action"></div>
                    <div class="additional_params"></div>
                </div>
                <button type="submit" class="{strip}{if $smarty.get.controller == 'TemplatesAdmin'
                        ||  $smarty.get.controller == 'StylesAdmin'
                        ||  $smarty.get.controller == 'ScriptsAdmin'}
                            fn_save{else}fast_save_button{/if}{/strip} btn btn_small btn_blue">
                    {include file='svg_icon.tpl' svgId='checked'}
                    <span>{$btr->general_apply|escape}</span>
                </button>
            </div>
        </div>

        {*Форма подтверждения действий*}
        <div id="fn_action_modal" class="modal fade show" role="document">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="card-header">
                        <div class="heading_modal">{$btr->index_confirm|escape}</div>
                    </div>
                    <div class="modal-body">
                        <button type="submit" class="btn btn_small btn_blue fn_submit_delete mx-h">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->index_yes|escape}</span>
                        </button>

                        <button type="button" class="btn btn_small btn-danger fn_dismiss_delete mx-h" data-dismiss="modal">
                            {include file='svg_icon.tpl' svgId='delete'}
                            <span>{$btr->index_no|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

{*main scripts*}

{$block = {get_design_block block="main_custom_block_before_js"}}
{if !empty($block)}
    <div>
        {$block}
    </div>
{/if}

{$ok_footer}

<script>
    $(function(){

        {if $config->dev_mode}
            // При нажатии на лейбл под названием секции меню происходит копирование в буфер обмена
            (function copyToBufferMenuSections() {
                $('.fn_backend_menu_section').on('click', function(e) {
                    e.preventDefault();
                    var sectionName = $(this).data('section_name'),
                        code = document.querySelector('.fn_backend_menu_section[data-section_name="' + sectionName + '"]'),
                        range = document.createRange();

                    range.selectNode(code);
                    window.getSelection().addRange(range);

                    sectionHighlightAnimation($(this));

                    var successful = document.execCommand('copy'),
                        msg = "";

                    if (successful) {
                        toastr.success(msg, "{$btr->toastr_success|escape}");
                    } else {
                        toastr.error(msg, "{$btr->toastr_error|escape}");
                    }

                    window.getSelection().removeAllRanges();
                });
            })();

            $('.fn_design_block_name').parent().addClass('design_block_parent_element');
            $('.fn_design_block_name').on('mouseover', function () {
                $(this).parent().addClass('focus');
            });
            $('.fn_design_block_name').on('mouseout', function () {
                $(this).parent().removeClass('focus');
            });

            function sectionHighlightAnimation($element) {
                $element.css({
                    color: 'white',
                    transition: '0.3s'
                });

                setTimeout(function() {
                    $element.css({
                        color: 'red',
                    });
                }, 300);
            }

        {/if}
        
        /* Initializing the scrollbar */
        if($('.scrollbar-inner').size()>0){
            $('.scrollbar-inner').scrollbar({
                "disableBodyScroll":true
            });
        }

        if($(window).width() < 1199 ){
            if($('.scrollbar-variant').size()>0){
                $('.scrollbar-variant').scrollbar();
            }
        }
        if($('.input_file').size()>0){
            document.querySelector("html").classList.add('fn_input_file');

            var fileInput  = document.querySelector( ".input_file" ),
                button     = document.querySelector( ".input_file_trigger" ),
                the_return = document.querySelector(".input_file_return");

            button.addEventListener( "keydown", function( event ) {
                if ( event.keyCode == 13 || event.keyCode == 32 ) {
                    fileInput.focus();
                }
            });
            button.addEventListener( "click", function( event ) {
                fileInput.focus();
                return false;
            });
            fileInput.addEventListener( "change", function( event ) {
                the_return.innerHTML = this.value;
            });
        }

            if($('form.fn_fast_button').size()>0){
            {literal}
            
            // Связка селектов массовых действий
            $(document).on('change', '.fn_action_block:not(.fn_fast_action_block) select', function(e, trigger) {
                if (!trigger) {
                    var name = $(this).attr('name'),
                        selected = $(this).children(':selected').val();
                    $('.fn_fast_save select[name="' + name + '"]').val(selected).trigger('change', {trigger: true});
                }
            });
            
            $(document).on('change', '.fn_fast_save select', function(e, trigger) {
                if (!trigger) {
                    var name = $(this).attr('name'),
                        selected = $(this).children(':selected').val();
                    $('form.fn_fast_button select[name="' + name + '"]').val(selected).trigger('change', {trigger: true});
                }
            });
            {/literal}
            
            if ($('.fn_action_block').size()>0) {
                var action_block = $('.okay_list_option').clone(true);
                $('.fn_fast_action_block .action').html(action_block);
                if ($('.fn_additional_params').size()) {
                    var additional_params = $('.fn_additional_params').clone(true);
                    $('.fn_fast_action_block .additional_params').html(additional_params);
                }
            }
            
            $('input,textarea,select, .dropdown-toggle, .fn_sort_item, .fn_category_item').bind('keyup change dragover',function(){
               $('.fn_fast_save').show();
            });
            $('#fn_add_purchase').bind('click',function(){
                $('.fn_fast_save').show();
            });

            $('.fn_fast_save .fast_save_button').on('click', function () {
                $('body').find("form.fn_fast_button").trigger('submit');
            });
        }

        /* Check */
        if($('.fn_check_all').size()>0){
            $(document).on('change','.fn_check_all',function(){
                if($(this).is(":checked")) {
                    console.log($(this).closest("form").find('.hidden_check'))
                    $(this).closest("form").find('.hidden_check').each(function () {
                        if(!$(this).is(":checked")) {
                            $(this).trigger("click");
                        }
                    });
                } else {
                    $(this).closest("form").find('.hidden_check').each(function () {
                        if($(this).is(":checked")) {
                            $(this).trigger("click");
                        }
                    })
                }
            });
        }

        $( function(){
            $( ".fn_tooltips" ).tooltip();
        });

        /* Catalog items toggle */
        if($('.fn_item_switch').size()>0){
            $('.fn_item_switch').on('click',function(e){
                var parent = $(this).closest("ul"),
                    li = $(this).closest(".fn_item_sub_switch"),
                    sub = li.find(".fn_submenu_toggle");

                if(li.hasClass("open active")){

                    sub.slideUp(200, function () {
                        li.removeClass("open active")
                    })

                } else {
                    parent.find("li.open").children(".fn_submenu_toggle").slideUp(200),
                    parent.find("li.open").removeClass("open active"),
                    li.children(".arrow").addClass("open active"),

                    sub.slideDown(200, function () {
                        li.addClass("open active")
                    })
                }
            });
        }

        /* Left menu toggle */
        if($('.fn_switch_menu').size()>0){
            $(document).on("click", ".fn_switch_menu", function () {
                $("body").toggleClass("menu-pin");
            });
            $(document).on("click", ".fn_mobile_menu", function () {
                $("body").toggleClass("menu-pin");
                $(".fn_quickview").removeClass("open");
            });
        }

        /* Right menu toggle */
        if($('.fn_switch_quickview').size()>0){
            $(document).on("click", ".fn_mobile_menu_right", function () {
                $(this).next().toggleClass("open");
                $("body").removeClass("menu-pin");
            });
            $(document).on("click", ".fn_switch_quickview", function () {
                $(this).closest(".fn_quickview").toggleClass("open");
            });
        }

        /* Delete images for products */
        if($('.images_list').size()>0){
            $('.fn_delete').on('click',function(){
                if($('.fn_accept_delete').size()>0){
                    $('.fn_accept_delete').val('1');
                    $(this).closest("li").fadeOut(200, function() {
                        $(this).remove();
                    });
                } else {
                    $(this).closest("li").fadeOut(200, function() {
                        $(this).remove();
                    });
                }
                return false;
            });
        }

        {if $is_mobile === false && $is_tablet === false}
            {literal}
            Sortable.create(document.getElementById("fn_sort_menu_section"), {
                sort: true,  // sorting inside list
                animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                scrollSensitivity: 100, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10, // px
                // Changed sorting within list
                onUpdate: function (evt) {
                    save_menu();
                }
            });
    
            if($(".fn_sort_menu_item").size()>0) {
                $(".fn_sort_menu_item").each(function() {
                    Sortable.create(this, {
                        sort: true,  // sorting inside list
                        animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation.
                        scroll: true,
                        scrollSensitivity: 100, // px, how near the mouse must be to an edge to start scrolling.
                        scrollSpeed: 10, // px
                        // Changed sorting within list
                        onUpdate: function (evt) {
                            save_menu();
                        }
                    });
                });
            }
    
            function save_menu() {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "ajax/update_object.php",
                    data: $('.fn_manager_menu').serialize()
                });
            }
            {/literal}
        {/if}

        /* Initializing sorting */
        if($(".sortable").size()>0) {
            {literal}
            $(".sortable").each(function() {
                Sortable.create(this, {
                    handle: ".move_zone",  // Drag handle selector within list items
                    sort: true,  // sorting inside list
                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                    ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                    chosenClass: "sortable-chosen",  // Class name for the chosen item
                    dragClass: "sortable-drag",  // Class name for the dragging item
                    scrollSensitivity: 100, // px, how near the mouse must be to an edge to start scrolling.
                    scrollSpeed: 10, // px
                    
                    // Changed sorting within list
                    onUpdate: function (evt) {
                        if ($(".product_images_list").size() > 0) {
                            var itemEl = evt.item;  // dragged HTMLElement
                            if ($(itemEl).closest(".fn_droplist_wrap").data("image") == "product") {
                                $(".product_images_list").find("li.first_image").removeClass("first_image");
                                $(".product_images_list").find("li:nth-child(2)").addClass("first_image");
                            }
                        }
                    }
                });
            });
            {/literal}
        }

        if($(".sort_extended").size()>0) {
            
            /*Явно указываем высоту списка, иначе когда скрипт удаляет элемент и ставит на его место заглушку, страница подпрыгивает*/
            $(".fn_sort_list").css('min-height', $(".fn_sort_list").outerHeight());
            
            $(".sort_extended").sortable({
                items: ".fn_sort_item",
                tolerance: "pointer",
                handle: ".move_zone",
                scrollSensitivity: 50,
                scrollSpeed: 100,
                scroll: true,
                opacity: 0.5,
                containment: "document",
                helper: function(event, ui){
                    if ($('input[type="checkbox"][name*="check"]:checked').size()<1) return ui;
                    var helper = $('<div/>');
                    $('input[type="checkbox"][name*="check"]:checked').each(function() {
                        var item = $(this).closest('.fn_row');
                        helper.height(helper.height()+item.innerHeight());
                        if (item[0]!=ui[0]) {
                            helper.append(item.clone());
                            $(this).closest('.fn_row').remove();
                        } else {
                            helper.append(ui.clone());
                            item.find('input[type="checkbox"][name*="check"]').attr('checked', false);
                        }
                    });
                    return helper;
                },
                start: function(event, ui) {
                    if(ui.helper.children('.fn_row').size()>0)
                        $('.ui-sortable-placeholder').height(ui.helper.height());
                },
                beforeStop:function(event, ui){
                    if(ui.helper.children('.fn_row').size()>0){
                        ui.helper.children('.fn_row').each(function(){
                            $(this).insertBefore(ui.item);
                        });
                        ui.item.remove();
                    }
                },
                update: function (event, ui) {
                    $("#list_form input[name*='check']").attr('checked', false);

                }
            });
        }
        
        $(".fn_pagination a.droppable").droppable({
            activeClass: "drop_active",
            hoverClass: "drop_hover",
            tolerance: "pointer",
            drop: function(event, ui){
                $(ui.helper).find('input[type="checkbox"][name*="check"]').attr('checked', true);
                $(ui.draggable).closest("form").find('select[name="action"] option[value=move_to_page]').attr("selected", "selected");
                $(ui.draggable).closest("form").find('select[name=target_page] option[value='+$(this).html()+']').attr("selected", "selected");
                $(ui.draggable).closest("form").submit();
                return false;
            }
        });
        
        /* Call an ajax entity update */
        if($(".fn_ajax_action").size()>0){
            $(document).on("click",".fn_ajax_action",function () {
                ajax_action($(this));
            });
        }

        if($(".fn_parent_image").size()>0 ) {

            $(document).on("click", '.fn_delete_item', function () {
                $(this).closest(".fn_image_block").find(".fn_upload_image").removeClass("hidden");
                $(this).closest(".fn_image_block").find(".fn_accept_delete").val(1);
                $(this).closest(".fn_image_wrapper").remove()
            });

            if(window.File && window.FileReader && window.FileList) {

                $(".fn_upload_image").on('dragover', function (e){
                    e.preventDefault();
                    $(this).css('background', '#bababa');
                });
                $(".fn_upload_image").on('dragleave', function(){
                    $(this).css('background', '#f8f8f8');
                });
                function handleFileSelect(evt){
                    var parent = $(this).closest(".fn_image_block");
                    var parent_image = parent.find(".fn_parent_image");
                    var files = evt.target.files;
                    for (var i = 0, f; f = files[i]; i++) {
                        if (!f.type.match('image.*')) {
                            continue;
                        }
                        var reader = new FileReader();
                        reader.onload = (function(theFile) {
                            return function(e) {
                                var clone_image = parent.find(".fn_new_image").clone(true);
                                clone_image.removeClass("hidden");
                                clone_image.find('[type="hidden"]').prop("disabled", false);
                                clone_image.find("img").attr("src", e.target.result);
                                clone_image.find("img").attr("onerror", '$(this).closest(\"div\").remove()');
                                clone_image.appendTo(parent_image);
                                parent.find(".fn_upload_image").addClass("hidden");
                            };
                        })(f);
                        reader.readAsDataURL(f);
                    }
                    $(".fn_upload_image").removeAttr("style");
                }
                $(document).on('change','.dropzone_image', handleFileSelect);
            }
        }
    });

    $(document).on('click', '.fn_light_remove', function () {
        $(this).closest(".fn_row").remove();
    });
    if($('.fn_remove').size() > 0) {
        // Подтверждение удаления
        /*
        * функция модального окна с подтверждением удаления
        * принимает аргумент $this - по факту сама кнопка удаления
        * функция вызывается прямо в файлах tpl
        * */
        function success_action ($this){
            $(document).on('click','.fn_submit_delete',function(){
                $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
                $this.closest(".fn_row").find('input[type="checkbox"][name*="check"]').prop('checked', true);
                $this.closest(".fn_form_list").find('select[name="action"] option[value=delete]').prop('selected', true);
                $this.closest(".fn_form_list").submit();
            });
            $(document).on('click','.fn_dismiss_delete',function(){
                $('.fn_form_list input[type="checkbox"][name*="check"]').prop('checked', false);
                $this.closest(".fn_form_list").find('select[name="action"] option[value=delete]').removeAttr('selected');
                return false;
            });
        }
    }
    {literal}
        if($(".fn_ajax_action,.fn_ajax_block").size()>0) {
            /* Функция аяксового обновления полей
            * state - состояние объекта (включен/выключен)
            * id - id обновляемой сущности
            * controller - типо сущности
            * action - обновляемое поле (поле в БД)
            * класс "fn_ajax_block" у елемента - означает массовое обновление;
            * если нужно:
            * 1) добавить класс "fn_ajax_block" к блоку в котором хотите обновить несколько полей,
            * 2) добавить класс "fn_ajax_element" к елементам, в блоке("fn_ajax_block"), которые хотите обновить
            * .fn_ajax_element: аттрибут "name" - поле БД; val() - значение.
            * */
            function ajax_action($this) {
                var state, controller, session_id, action, id, values = {};
                state = $this.hasClass("fn_active_class") ? 0:1;
                id = parseInt($this.data('id'));
                controller = $this.data("controller");
                action = $this.data("action");
                session_id = '{/literal}{$smarty.session.id}{literal}';
                if (!$this.hasClass("fn_ajax_block")) {
                    values = {[action]: state};
                } else {
                    $this.find('.fn_ajax_element').each(function() {
                        var elem = $(this);
                        values[elem.attr('name')] = elem.val();
                    });
                }
                toastr.options = {
                    closeButton: true,
                    newestOnTop: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    preventDuplicates: false,
                    onclick: null
                };
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "ajax/update_object.php",
                    data: {
                        object : controller,
                        id : id,
                        values: values,
                        session_id : session_id
                    },
                    success: function(data){
                        var msg = "";
                        if(data){
                            toastr.success(msg, "{/literal}{$btr->toastr_success|escape}{literal}");
                            if (action == "processed" && controller == "callback") {
                                $this.closest(".fn_row").find(".fn_callbacks_toggle").toggleClass("hidden");
                            } else {
                                $this.toggleClass("fn_active_class");
                                if (action == "approved" || action == "processed") {
                                    $this.closest("div").find(".fn_answer_btn").show();
                                }
                            }
                        } else {
                            toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                        }
                    }
                });
                return false;
            }
        }
    {/literal}

    /*
    * функции генерации мета данных
    * */
    var is_translit_alpha = $(".fn_is_translit_alpha");
    var translit_pairs = [];
    {foreach $translit_pairs as $i=>$pair}
        translit_pairs[{$i}] = {
            from: "{$pair['from']}".split("-"),
            to: "{$pair['to']}".split("-")
        };
    {/foreach}
    if($('input').is('.fn_meta_field')) {
        $(window).on("load", function() {

            // Автозаполнение мета-тегов
            meta_title_touched = true;
            meta_keywords_touched = true;
            meta_description_touched = true;

            if($('input[name="meta_title"]').val() == generate_meta_title() || $('input[name="meta_title"]').val() == '')
                meta_title_touched = false;
            if($('input[name="meta_keywords"]').val() == generate_meta_keywords() || $('input[name="meta_keywords"]').val() == '')
                meta_keywords_touched = false;
            if($('textarea[name="meta_description"]').val() == generate_meta_description() || $('textarea[name="meta_description"]').val() == '')
                meta_description_touched = false;

            $('input[name="meta_title"]').change(function() { meta_title_touched = true; });
            $('input[name="meta_keywords"]').change(function() { meta_keywords_touched = true; });
            $('textarea[name="meta_description"]').change(function() { meta_description_touched = true; });

            $('#fn_meta_title_counter').text( '('+$('input[name="meta_title"]').val().length+')' );
            $('#fn_meta_description_counter').text( '('+$('textarea[name="meta_description"]').val().replace(/\n/g, "\r\n").length+')' );

            $('input[name="name"]').keyup(function() { set_meta(); });
            $('input[name="meta_title"]').keyup(function() { $('#fn_meta_title_counter').text( '('+$('input[name="meta_title"]').val().length+')' ); });
            $('textarea[name="meta_description"]').keyup(function() { $('#fn_meta_description_counter').text( '('+$('textarea[name="meta_description"]').val().replace(/\n/g, "\r\n").length+')' ); });

            if($(".fn_meta_brand").size()>0) {
                $("select[name=brand_id]").on("change",function () {
                    set_meta();
                })
            }
            if($(".fn_meta_author").size()>0) {
                $("select[name=author_id]").on("change",function () {
                    set_meta();
                })
            }
            if($(".fn_meta_categories").size()>0) {
                $(".fn_meta_categories").on("change",function () {
                    set_meta();
                })
            }
        });
        
        function set_meta() {
            if(!meta_title_touched)
                $('input[name="meta_title"]').val(generate_meta_title());
            if(!meta_keywords_touched)
                $('input[name="meta_keywords"]').val(generate_meta_keywords());
            if(!meta_description_touched)
                $('textarea[name="meta_description"]').val(generate_meta_description());
            if(!$('#block_translit').is(':checked'))
                $('input[name="url"]').val(generate_url());
        }

        function generate_meta_title() {
            name = $('input[name="name"]').val();
            $('#fn_meta_title_counter').text( '('+name.length+')' );
            return name;
        }

        function generate_meta_keywords() {
            let result = $('input[name="name"]').val();
            
            if ($(".fn_meta_brand").size() > 0) {
                let brand = $('select[name="brand_id"] option:selected').data('brand_name');
                if (typeof(brand) == 'string' && brand != '')
                    result += ', ' + brand;
            }
            if ($(".fn_meta_author").size() > 0) {
                let author = $('select[name="author_id"] option:selected').data('author_name');
                if (typeof(author) == 'string' && author != '')
                    result += ', ' + author;
            }

            if($(".fn_meta_categories").size()>0) {
                if($(".fn_product_categories_list .fn_category_item").size() == 0) {
                    let c = $(".fn_meta_categories option:selected").data("category_name");
                    if (typeof(c) == 'string' && c != '')
                        result += ', ' + c;
                } else {
                    let cat = $(".fn_product_categories_list .fn_category_item:first");
                    let c = cat.find("input").data("cat_name");
                    if (typeof(c) == 'string' && c != '')
                        result += ', ' + c;
                }

            }
            return result;
        }

        function generate_meta_description() {
            if(typeof(tinyMCE.get("fn_editor")) =='object') {
                description = tinyMCE.get("fn_editor").getContent().replace(/(<([^>]+)>)/ig," ").replace(/\n/g, "\r\n").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
                $('#fn_meta_description_counter').text( '('+description.length+')');
                return description;
            } else {
                return $('.fn_editor_class').val().replace(/(<([^>]+)>)/ig," ").replace(/\n/g, "\r\n").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
            }
        }
    }

    function generate_url() {
        url = $('input[name="name"]').val();
        url = translit(url);
        if (is_translit_alpha.size() > 0) {
            url = url.replace(/[^0-9a-z]+/gi, '').toLowerCase();
        } else {
            url = url.replace(/[\s]+/gi, '-');
            url = url.replace(/[^0-9a-z_\-]+/gi, '').toLowerCase();
        }
        return url;
    }

    function translit(str) {
        var str_tm = str;
        for (var j in translit_pairs) {
            var from = translit_pairs[j].from,
                to = translit_pairs[j].to,
                res = '';
            for(var i=0, l=str_tm.length; i<l; i++) {
                var s = str_tm.charAt(i), n = from.indexOf(s);
                if(n >= 0) { res += to[n]; }
                else { res += s; }
            }
            str_tm = res;
        }
        return str_tm;
    }
    /*функции генерации мета данных end*/

    $(window).on('load',function () {

        $("#countries_select").msDropdown({
            roundedBorder:false
        });

        /*
        * Скрипт табов
        * */
        $('.tabs').each(function(i) {
            var cur_nav = $(this).find('.tab_navigation'),
                cur_tabs = $(this).find('.tab_container'),
                cur_tab;

            {if $smarty.get.active_tab}
                cur_nav.children().removeClass('selected');
                cur_nav.children('[href="#{$smarty.get.active_tab|escape}"]').addClass('selected');
            {/if}
            
            if (cur_nav.children('.selected').size() > 0) {
                cur_tab = $(cur_nav.children('.selected').attr("href"));
            } else {
                cur_nav.children().first().addClass('selected');
                cur_tab = cur_tabs.children().first();
            }
            cur_tab.show();
        });

        $('.tab_navigation_link').click(function(e){
            e.preventDefault();
            if($(this).hasClass('selected')){
                return true;
            }
            var cur_nav = $(this).closest('.tabs').find('.tab_navigation'),
                cur_tabs = $(this).closest('.tabs').find('.tab_container'),
                cur_tab = $($(this).attr("href"));
            cur_tabs.children().hide();
            cur_nav.children().removeClass('selected');
            $(this).addClass('selected');

            let newUrl;
            if (window.location.href.indexOf('active_tab') !== -1) {
                newUrl = window.location.href.replace(/([?&]active_tab)=([^#&]*)/g, '$1=' + cur_tab.attr('id'));
            } else {
                newUrl = window.location + '&active_tab=' + cur_tab.attr('id');
            }

            history.pushState(null, null, newUrl);
            cur_tab.fadeIn(200);
        });
        /*Скрипт табов end*/

        /*
        * скрипт сворачивания информационных блоков
        * */
        $(document).on("click", ".fn_toggle_card", function () {
            $(this).closest(".fn_toggle_wrap").find('.fn_icon_arrow').toggleClass('rotate_180');
            $(this).closest(".fn_toggle_wrap").find(".fn_card").slideToggle(500);
        });

        /*
        * скрипт отображения загрузки модуля
        * */
        $(document).on("click", ".fn_switch_add_module", function () {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            }
            else {
                $(this).addClass('active');
            }

            $(".fn_hide_add_module").slideToggle(500);
        return false;
        });

        /*
        * Блокировка автоформирования ссылки
        * */
        $(document).on("click", ".fn_disable_url", function () {
            if($(".fn_url").attr("readonly")){
                $(".fn_url").removeAttr("readonly");
            } else {
                $(".fn_url").attr("readonly",true);
            }
            $(this).find('i').toggleClass("fa-unlock");
            $("#block_translit").trigger("click");
        });
        /*Блокировка автоформирования ссылки end*/

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
            $('.selectpicker').selectpicker('mobile');
        }
    });
    
</script>

{$block = {get_design_block block="main_custom_block_after_js"}}
{if !empty($block)}
    <div>
        {$block}
    </div>
{/if}
</html>
