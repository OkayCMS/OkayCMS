{$meta_title = $btr->images_images scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->images_theme|escape} {$theme|escape}
            </div>
        </div>
    </div>
</div>

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_error == 'permissions'}
                        {$btr->general_permissions|escape} {$images_dir|escape}
                        {elseif $message_error == 'name_exists'}
                        {$btr->images_exists|escape}
                        {elseif $message_error == 'theme_locked'}
                        {$btr->general_protected|escape}
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
            <input type="hidden" name="delete_image" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading_box">
                            {$btr->images_images|escape}
                            <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                                <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                            </div>
                        </div>
                        {*Параметры элемента*}
                        <div class="toggle_body_wrap fn_card on">
                            <div class="row">
                                {foreach $images as $image}
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                        <div class="banner_card">
                                            <div class="banner_card_header">
                                                <input type="text" class="hidden" name="old_name[]" value="{$image->name|escape}">
                                                <div class="form-group col-lg-9 col-md-8 px-0 fn_rename_value hidden mb-0">
                                                    <input type="text" class="form-control" name="new_name[]" value="{$image->name|escape}">
                                                </div>
                                                <span class="font-weight-bold">{$image->name|escape|truncate:20:'...'}</span>
                                                <i class="fa fa-pencil fn_rename_theme rename_theme p-h" data-old_name="{$image->name|escape}"></i>

                                                <button type="button" data-name="{$image->name}" class="fn_delete_image btn_close float-xs-right">
                                                    {include file='svg_icon.tpl' svgId='delete'}
                                                </button>
                                            </div>
                                            <div class="banner_card_block">
                                                <div class="wrap_bottom_tag_images">
                                                    <a class="theme_image_item" href='../{$images_dir}{$image->name|escape}'>
                                                        <img src='../{$images_dir}{$image->name|escape}'>
                                                    </a>
                                                    <div class="tag tag-info">
                                                        {if $image->size>1024*1024}
                                                            {($image->size/1024/1024)|round:2} {$btr->general_mb|escape}
                                                        {elseif $image->size>1024}
                                                            {($image->size/1024)|round:2} {$btr->general_kb|escape}
                                                        {else}
                                                            {$image->size} {$btr->general_byte|escape}
                                                        {/if},
                                                        {$image->width}&times;{$image->height} px
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7 col-md-7">
                        <div class="">
                            <button type="button" class="fn_add_image btn btn_small btn-info mb-1 btn_images_add">
                                {include file='svg_icon.tpl' svgId='plus'}
                                {$btr->images_add|escape}
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-5 pull-right">
                        <button type="submit" name="save" class="btn btn_small btn_blue float-md-right">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{* On document load *}
<script>
    var general_confirm_delete = '{$btr->general_confirm_delete|escape}';
</script>
{literal}
<script>
$(function() {

    $('.fn_rename_theme').on('click',function(){
        $(this).parent().find('.fn_rename_value').toggleClass('hidden');
        $(this).prev().toggleClass('hidden');
        $(this).parent().find('.fn_rename_value > input').val($(this).data('old_name'))
    });
    // Удалить
    $('.fn_delete_image').on('click',function(){
        $('input[name=delete_image]').val($(this).data('name'));
        $('form').submit();
    });
    
    // Загрузить
    $('.fn_add_image').on('click',function(){
        $(this).closest('div').append($('<input class="import_file" type="file" name="upload_images[]">'));
    });
    
    $("form").submit(function() {
        if($('input[name="delete_image"]').val()!='' && !confirm(general_confirm_delete))
            return false;    
    });

});
</script>
{/literal}
