{if $theme->name}
    {$meta_title = "`$btr->general_theme` {$theme->name}" scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page ">
                {$btr->theme_current|escape} {$theme->name}
            </div>
            <div class="box_btn_heading theme_btn_heading">
                <a class="fn_clone_theme btn btn_small btn-info" href="/">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->theme_copy|escape} {$settings->theme}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{if $theme->locked}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {$btr->theme_close|escape}
                    </div>
                </div>
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
                    {if $message_error == 'permissions'}
                        {$btr->general_permissionse|escape} {$themes_dir}
                    {elseif $message_error == 'name_exists'}
                        {$btr->theme_exists|escape}
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
<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="session_id" value="{$smarty.session.id}">
                <input type="hidden" name="action">
                <input type="hidden" name="theme">

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="heading_box">
                            {$btr->theme_themes|escape}
                            <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                                <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                            </div>
                        </div>
                        <div class="toggle_body_wrap fn_card on">
                            <div class="row">
                                {foreach $themes as $t}
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="banner_card">
                                            <div class="banner_card_header img_bnr_c_head">
                                                <input type="text" class="hidden" name="old_name[]" value="{$t->name|escape}">
                                                <div class="form-group col-lg-9 col-md-8 px-0 fn_rename_value hidden mb-0">
                                                    <input type="text" class="form-control" name="new_name[]" value="{$t->name|escape}">
                                                </div>
                                                <span class="theme_active_span font-weight-bold">{$t->name|escape|truncate:20:'...'} {if  $t->name == $theme->name}<span class="text_success">- {$btr->theme_current_item|escape} </span>{/if}</span>
                                                {if  !$t->locked}
                                                    <i class="fa fa-pencil fn_rename_theme rename_theme p-h" data-old_name="{$t->name|escape}"></i>
                                                {/if}

                                                {if !$t@first}
                                                    <button data-theme_name="{$t->name|escape}" type="button" class="btn_close float-xs-right fn_remove_theme" data-toggle="modal" data-target="#fn_delete_theme">
                                                        {include file='svg_icon.tpl' svgId='delete'}
                                                    </button>
                                                {/if}
                                            </div>
                                            <div class="banner_card_block">
                                                <div class="theme_block_image" style="position:relative;">
                                                    <img class="{if $theme->name != $t->name}gray_filter{/if}" width="" src='{$root_dir}../design/{$t->name}/preview.png'>
                                                    {if $theme->name != $t->name}
                                                        <div class="fn_set_theme btn btn_small btn_blue theme_btn_admin" data-set_name="{$t->name|escape}">
                                                            {include file='svg_icon.tpl' svgId='checked'}
                                                            <span>{$btr->general_select|escape}</span>
                                                        </div>
                                                    {/if}
                                                    {if $t->name == $settings->admin_theme}
                                                        <button type="button" value="" class="btn btn_small btn-danger fn_set_admin theme_btn_block" data-set_name="{$t->name|escape}">
                                                            {include file='svg_icon.tpl' svgId='delete'}
                                                            <span>{$btr->theme_unset_to_admin}</span>
                                                        </button>
                                                    {else}
                                                        <button type="button" value="{$t->name|escape}" class="btn btn_small btn_blue fn_set_admin theme_btn_block" data-set_name="{$t->name|escape}">
                                                            {include file='svg_icon.tpl' svgId='checked'}
                                                            <span>{$btr->theme_set_to_admin}</span>
                                                        </button>
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="boxed fn_toggle_wrap">
                            <div class="heading_box">
                                {$btr->restrict_to_admins}
                            </div>
                            <div class="toggle_body_wrap on fn_card">
                                <div class="permission_block">
                                    <div class="permission_boxes row fn_perms_wrap">
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="permission_box">
                                                <span>{$btr->theme_all_admins}</span>
                                                <label class="switch switch-default">
                                                    <input class="switch-input fn_all_managers" name="admin_theme_managers" value="all" type="checkbox" {if !$admin_theme_managers}checked{/if} />
                                                    <span class="switch-label"></span>
                                                    <span class="switch-handle"></span>
                                                </label>
                                            </div>
                                        </div>
                                        {foreach $managers as $m}
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <div class="permission_box">
                                                    <span>{$m->login}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input fn_manager" name="admin_theme_managers[]" value="{$m->login}" type="checkbox" {if $admin_theme_managers && in_array($m->login, $admin_theme_managers)}checked{/if} />
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                                <div class="col-xs-12 clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="admin_theme" disabled value="" />
                    <div class="col-lg-12">
                        <button type="submit" name="save" class="btn btn_small btn_blue fn_chek_all float-md-right ">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{*Форма подтверждения действия*}
<div id="fn_delete_theme" class="modal fade show" role="document">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-header">
                <div class="heading_modal">{$btr->theme_perform|escape}</div>
            </div>
            <div class="modal-body">
                <button type="submit" class="btn btn_small btn_blue fn_submit_delete">{$btr->theme_perform_yes|escape}</button>
                <button type="button" class="btn btn_small btn_blue fn_dismiss_delete" data-dismiss="modal">{$btr->theme_perform_no|escape}</button>
            </div>
        </div>
    </div>
</div>

<script>
    {literal}

    $(function() {

        $('.fn_all_managers').on('change', function(){
            $('.fn_manager').attr('checked', false);
        });
        $('.fn_manager').on('change', function(){
            $('.fn_all_managers').attr('checked', false);
        });
        $('.fn_set_admin').on('click', function (e) {
            e.preventDefault();
            $("input[name=admin_theme]").val($(this).val()).attr('disabled', false);
            $("form").submit();
        });

        $('.fn_rename_theme').on('click',function(){
            $(this).parent().find('.fn_rename_value').toggleClass('hidden');
            $(this).prev().toggleClass('hidden');
            $(this).parent().find('.fn_set_theme').toggleClass('opacity_toggle');
            $(this).parent().find('.fn_rename_value > input').val($(this).data('old_name'))
        });
        $('.fn_set_theme').on('click',function(){
            $("input[name=action]").val('set_main_theme');
            $("input[name=theme]").val($(this).data('set_name'));
            $("form").submit();
        });
        // Клонировать текущую тему
        $('.fn_clone_theme').on('click',function(e){
            e.preventDefault();
            $("input[name=action]").val('clone_theme');
            $("form").submit();
        });

        $(".fn_remove_theme").on("click", function () {
            action = "delete_theme";
            theme_name = $(this).data("theme_name");
        });
        $(".fn_submit_delete").on("click",function () {
            $("form input[name=action]").val(action);
            $("form input[name=theme]").val(theme_name);
            $("form").submit();
        });
        $(".fn_dismiss_delete").on("click",function () {
            $("form input[name=action]").val("");
            $("form input[name=theme]").val("");
        });

    });
    {/literal}
</script>
