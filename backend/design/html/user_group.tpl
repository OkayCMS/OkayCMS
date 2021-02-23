{if $group->id}
    {$meta_title = $group->name scope=global}
{else}
    {$meta_title = $btr->user_group_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$group->id}
                    {$btr->user_group_add|escape}
                {else}
                    {$group->name|escape}
                {/if}
            </div>
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
                    {if $message_success=='added'}
                        {$btr->general_group_added|escape}
                    {elseif $message_success=='updated'}
                        {$btr->user_group_updated|escape}
                    {else}
                        {$message_success|escape}
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
                    {if $message_error=='empty_name'}
                        {$btr->general_enter_title|escape}
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
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="session_id" value="{$smarty.session.id}">

    <div class="row">
        <div class="col-lg-12">
            <div class="boxed match_matchHeight_true">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="heading_label">
                            {$btr->user_group_name|escape}
                        </div>
                        <div class="form-group">
                            <input class="form-control" name="name" type="text" value="{$group->name|escape}"/>
                            <input name="id" type="hidden" value="{$group->id|escape}"/>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="heading_label">
                            {$btr->general_discount|escape}
                        </div>
                        <div class="form-group">
                             <input name="discount" class="form-control" type="text" value="{$group->discount|escape}" />
                        </div>
                    </div>
                    {get_design_block block="group_info"}
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 mt-1">
                        <button type="submit" class="btn btn_small btn_blue float-md-right">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
