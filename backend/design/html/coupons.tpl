{* Title *}
{$meta_title = $btr->coupons_coupons scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if $coupons_count}
                    {$btr->coupons_coupons} - {$coupons_count}
                {/if}
            </div>
            <div class="box_btn_heading fn_add_coupon">
                <button class="btn btn_small btn-info">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->coupons_add|escape}</span>
                </button>
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
                    {if $message_success == 'added'}
                        {$btr->coupons_added|escape}
                    {elseif $message_success == 'updated'}
                        {$btr->coupons_update|escape}
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
                    {if $message_error == 'code_exists'}
                        {$btr->coupons_exists|escape}
                    {elseif $message_error=='empty_code'}
                        {$btr->coupons_enter_code|escape}
                    {else}
                        {$message_error|escape}
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

{$block = {get_design_block block="coupons_custom_block"}}
{if !empty($block)}
    <div class="fn_toggle_wrap custom_block">
        {$block}
    </div>
{/if}


{*Главная форма страницы*}
{if $coupons}
    <div class="boxed fn_toggle_wrap">
        <form class="fn_form_list" method="post">
            <input type="hidden" name="session_id" value="{$smarty.session.id}">
            <div class="okay_list products_list fn_sort_list">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_coupon_name">{$btr->coupons_name|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_sale">{$btr->general_discount|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_condit">{$btr->general_conditions|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_validity">{$btr->coupons_terms|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_disposable">{$btr->coupons_one_off|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_count">{$btr->coupons_qty_uses|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>
                {*Блок добавления нового элемента*}
                <div class="okay_list_body fn_new_coupon">
                    <div class="okay_list_body_item">
                        <div class="okay_list_row ">
                            <div class="okay_list_heading okay_list_check"></div>
                            <div class="okay_list_boding okay_list_coupon_name">
                                <input class="form-control" name="new_code" type="text" value="" placeholder="{$btr->coupons_enter_name|escape}"/>
                                <input name="new_id" type="hidden" value=""/>
                            </div>
                            <div class="okay_list_boding okay_list_coupon_sale">
                                <div class="input-group">
                                    <input class="form-control" name="new_value" type="text" value="" />
                                    <select class="selectpicker form-control" name="new_type">
                                        <option value="percentage">%</option>
                                        <option value="absolute">{$currency->sign}</option>
                                    </select>
                                </div>

                            </div>
                            <div class="okay_list_boding okay_list_coupon_condit">
                                <input class="form-control" type="text" name="new_min_order_price" value="" placeholder="{$btr->coupons_order_price|escape}">
                            </div>
                            <div class="okay_list_boding okay_list_coupon_validity">
                                <div class="input-group">
                                    <input class="form-control" type=text name="new_expire" value="">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="okay_list_boding okay_list_coupon_disposable">
                                <input class="hidden_check" type="checkbox" name="new_single" id="single" value="1" />
                                <label class="okay_ckeckbox" for="single"></label>
                            </div>
                            <div class="okay_list_heading okay_list_coupon_count"></div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="okay_list_body fn_coupon_wrap">
                    {foreach $coupons as $coupon}
                        <div class="fn_row okay_list_body_item">
                            <div class="okay_list_row ">
                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$coupon->id}" name="check[]" value="{$coupon->id}"/>
                                    <label class="okay_ckeckbox" for="id_{$coupon->id}"></label>
                                </div>
                                <div class="okay_list_boding okay_list_coupon_name">
                                    <span class="text_dark">
                                        {$coupon->code}
                                    </span>
                                    <div class="hidden-lg-up mt-q">
                                        {if $coupon->expire}
                                            {if $smarty.now|date_format:'%Y%m%d' <= $coupon->expire|date_format:'%Y%m%d'}
                                                <span class="tag tag-primary">
                                                    {$btr->coupons_valid_until|escape} {$coupon->expire|date}
                                                </span>
                                            {else}
                                                <span class="tag tag-danger">
                                                    {$btr->coupons_expired|escape} {$coupon->expire|date}
                                                </span>
                                            {/if}
                                        {else}
                                            <span class="tag tag-warning">
                                                {include file='svg_icon.tpl' svgId='infinity'}
                                            </span>
                                        {/if}
                                        {if $coupon->min_order_price>0}
                                            <span class="tag tag-success">
                                                {$btr->coupons_order_from|escape} {$coupon->min_order_price|escape} {$currency->sign|escape}
                                            </span>
                                        {/if}
                                        <div class="mt-q">
                                            {if $coupon->single}
                                                {$btr->coupons_one_off|escape}
                                            {else}
                                                {$btr->coupons_many|escape}
                                            {/if}
                                        </div>

                                    </div>

                                    {get_design_block block="coupons_item" vars=['coupon' => $coupon]}
                                </div>
                                <div class="okay_list_boding okay_list_coupon_sale">
                                    {$coupon->value*1}
                                    {if $coupon->type=='absolute'}
                                        {$currency->sign|escape}
                                    {else}
                                        %
                                    {/if}
                                </div>
                                <div class="okay_list_boding okay_list_coupon_condit">
                                    {if $coupon->min_order_price>0}
                                        <div class="">
                                            {$btr->coupons_order_from|escape} {$coupon->min_order_price|escape} {$currency->sign|escape}
                                        </div>
                                    {/if}
                                </div>
                                <div class="okay_list_boding okay_list_coupon_validity">
                                    <div class="">
                                        {if $coupon->expire}
                                            {if $smarty.now|date_format:'%Y%m%d' <= $coupon->expire|date_format:'%Y%m%d'}
                                                {$btr->coupons_valid_until|escape} {$coupon->expire|date}
                                            {else}
                                                {$btr->coupons_expired|escape} {$coupon->expire|date}
                                            {/if}
                                        {else}
                                            {include file='svg_icon.tpl' svgId='infinity'}
                                        {/if}
                                    </div>
                                </div>
                                <div class="okay_list_boding okay_list_coupon_disposable">
                                    {if $coupon->single}
                                        {$btr->coupons_yes|escape}
                                    {else}
                                        {$btr->coupons_no|escape}
                                    {/if}
                                </div>
                                <div class="okay_list_boding okay_list_coupon_count">
                                    {if $coupon->usages>0}
                                         {$coupon->usages|escape}
                                    {else}
                                         0
                                    {/if}
                                </div>
                                <div class="okay_list_boding okay_list_close">
                                    {*delete*}
                                    <button data-hint="{$btr->coupons_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
{else}
    {*Главная форма страницы*}
    <div class="boxed fn_toggle_wrap">
        <form method="post" class="clearfix">
            <input type="hidden" name="session_id" value="{$smarty.session.id}">

            <div class="okay_list products_list fn_sort_list">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_heading okay_list_check"></div>
                    <div class="okay_list_heading okay_list_coupon_name">{$btr->coupons_name|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_sale">{$btr->general_discount|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_condit">{$btr->general_conditions|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_validity">{$btr->coupons_terms|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_disposable">{$btr->coupons_one_off|escape}</div>
                    <div class="okay_list_heading okay_list_coupon_count">{$btr->coupons_qty_uses|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>
                {*Параметры элемента*}
                <div class="okay_list_body">
                    <div class="okay_list_body_item">
                        <div class="okay_list_row ">
                            <div class="okay_list_heading okay_list_check"></div>
                            <div class="okay_list_boding okay_list_coupon_name">
                                <input class="form-control" name="new_code" type="text" value="" placeholder="{$btr->coupons_enter_name|escape}"/>
                                <input name="new_id" type="hidden" value=""/>
                            </div>
                            <div class="okay_list_boding okay_list_coupon_sale">
                                <div class="input-group">
                                    <input class="form-control" name="new_value" type="text" value="" />
                                    <select class="selectpicker form-control" name="new_type">
                                        <option value="percentage">%</option>
                                        <option value="absolute">{$currency->sign}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="okay_list_boding okay_list_coupon_condit">
                                <input class="form-control" type="text" name="new_min_order_price" value="" placeholder="{$btr->coupons_order_price|escape}">
                            </div>
                            <div class="okay_list_boding okay_list_coupon_validity">

                                <div class="input-group">
                                    <input class="form-control" type=text name="new_expire" value="">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="okay_list_boding okay_list_coupon_disposable">
                                <input class="hidden_check" type="checkbox" name="new_single" id="single" value="1" />
                                <label class="okay_ckeckbox" for="single"></label>
                            </div>
                            <div class="okay_list_heading okay_list_coupon_count"></div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
               <div class="col-lg-12 col-md-12 mt-1">
                    <button type="submit" class="btn btn_small btn_blue float-md-right">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </form>
       <script>
           $('input[name="new_expire"]').datepicker();
       </script>
    </div>
{/if}


{literal}
    <script>
        $(function() {
            var new_coupon = $(".fn_new_coupon").clone(true);
            $(".fn_new_coupon").remove();

            $(document).on("click", ".fn_add_coupon", function () {
                $(this).remove();
                new_coupon.find("select").selectpicker();
                new_coupon.find('input[name="new_expire"]').datepicker();
                $(".fn_coupon_wrap").prepend(new_coupon);
            })
        });
    </script>
{/literal}
