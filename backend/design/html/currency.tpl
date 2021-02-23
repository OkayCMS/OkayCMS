{$meta_title = $btr->currency_currencies scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->currency_currencies|escape}
            </div>
            <div class="box_btn_heading">
                <a id="add_currency" class="btn btn_small btn-info" href="#">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->currency_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon alert--error">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_error|escape}</div>
                <p>{$btr->coupon_alert_text|escape}</p>
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
                        {if $message_error == 'wrong_iso'}
                        Недопустимый код ISO в
                        {foreach $wrong_iso as $w_iso}
                        <div>{$w_iso|escape}</div>
                        {/foreach}
                        {else}
                        {$message_error|escape}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{$block = {get_design_block block="currency_custom_block"}}
{if $block}
    <div class="custom_block">
        {$block}
    </div>
{/if}

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <form method=post class="fn_form_list">
                <input type="hidden" name="session_id" value="{$smarty.session.id}">
                <input type="hidden" name="lang_id" value="{$lang_id}" />
                <div class="okay_list fn_step-2">
                    <div class="fn_step-1 currencies_wrap clearfix">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_currency_num">ID</div>
                            <div class="okay_list_heading okay_list_currency_name">{$btr->currency_name|escape}</div>
                            <div class="okay_list_heading okay_list_currency_sign">{$btr->currency_symbol|escape}</div>
                            <div class="okay_list_heading okay_list_currency_iso">{$btr->currency_iso|escape}</div>
                            <div class="okay_list_heading okay_list_currency_exchange">{$btr->currency_rate|escape}</div>
                            <div class="okay_list_heading okay_list_status hidden-md-down">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading cur_settings">{$btr->general_activities|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        {*Параметры элемента*}
                        <div id="currencies_block" class="okay_list_body sortable">
                            {foreach $currencies as $c}
                                <div class="fn_step-2 okay_list_body_item">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_num">
                                            <span>{$c->id}</span>
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_name">
                                            <input class="form-control" name="currency[id][{$c->id}]" type="hidden" value="{$c->id|escape}"/>
                                            <input name="currency[name][{$c->id}]" class="form-control" type="text" value="{$c->name|escape}"/>

                                            {if $c@first}
                                                <span data-hint="{$btr->currency_base|escape}" class="currency_name_active hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                    Основная валюта
                                                    {*include file='svg_icon.tpl' svgId='checked'*}
                                                </span>
                                            {/if}
                                            {if $is_mobile == true}
                                            <div class="hidden-md-up mt-q">
                                                <div class="okay_list_currency_exchange_item">
                                                    {*if !$c@first*}
                                                        <div class="input-group">
                                                            <div class="input-group-qw cur_input_exchange">
                                                                <div class="input-group">
                                                                    <input class="form-control"  name="currency[rate_from][{$c->id}]" type="text" value="{$c->rate_from|escape}"/>
                                                                    <span class="input-group-addon">{$c->sign|escape}</span>
                                                                </div>
                                                            </div>

                                                            <div class="input-group-qw"><span class="equality">=</span></div>

                                                            <div class="input-group-qw cur_input_exchange">
                                                                <div class="input-group">
                                                                   <input class="form-control"  name="currency[rate_to][{$c->id}]" type="text" value="{$c->rate_to|escape}"/>
                                                                   <span class="input-group-addon">{$currency->sign|escape}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {*else}
                                                        <input name="currency[rate_from][{$c->id}]" type="hidden" value="{$c->rate_from|escape}"/>
                                                        <input name="currency[rate_to][{$c->id}]" type="hidden" value="{$c->rate_to|escape}"/>
                                                    {/if*}
                                                </div>
                                            </div>
                                            {/if}
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_iso">
                                            <input class="form-control" name="currency[sign][{$c->id}]" type="text" value="{$c->sign|escape}"/>
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_sign">
                                            <input class="form-control" name="currency[code][{$c->id}]" type="text" value="{$c->code|escape}"/>
                                        </div>
                                        {if $is_mobile == false}
                                        <div class="okay_list_boding okay_list_currency_exchange">
                                            <div class="okay_list_currency_exchange_item">
                                                {*if !$c@first*}
                                                    <div class="input-group">
                                                        <div class="input-group-qw cur_input_exchange">
                                                            <div class="input-group">
                                                                <input class="form-control"  name="currency[rate_from][{$c->id}]" type="text" value="{$c->rate_from|escape}"/>
                                                                <span class="input-group-addon">{$c->sign}</span>
                                                            </div>
                                                        </div>

                                                        <div class="input-group-qw"> <span class="equality">=</span> </div>

                                                        <div class="input-group-qw cur_input_exchange">
                                                            <div class="input-group">
                                                               <input class="form-control"  name="currency[rate_to][{$c->id}]" type="text" value="{$c->rate_to|escape}"/>
                                                               <span class="input-group-addon">{$currency->sign}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {*else}
                                                    <input name="currency[rate_from][{$c->id}]" type="hidden" value="{$c->rate_from|escape}"/>
                                                    <input name="currency[rate_to][{$c->id}]" type="hidden" value="{$c->rate_to|escape}"/>
                                                {/if*}
                                            </div>
                                            {get_design_block block="currency_item" vars=['c' => $c]}
                                        </div>
                                        {/if}
                                        <div class="okay_list_boding okay_list_status hidden-md-down">
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $c->enabled}fn_active_class{/if}" data-controller="currency" data-action="enabled" data-id="{$c->id}" name="enabled" value="1" type="checkbox"  {if $c->enabled}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                        <div class="cur_settings">
                                            <button data-hint="{$btr->currency_cents_display|escape}" type="button" class="setting_icon setting_icon_yandex hint-bottom-middle-t-info-s-small-mobile hint-anim fn_ajax_action {if $c->cents}fn_active_class{/if}" data-controller="currency" data-action="cents" data-id="{$c->id}" name="cents">
                                                <i class="fa fa-database fa-sm m-t-2"></i>
                                            </button>
                                        </div>
                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            {if !$c@first}
                                                <button data-hint="{$btr->currency_delete|escape}" type="button" class=" btn_close fn_remove_currency hint-bottom-right-t-info-s-small-mobile  hint-anim" data-id="{$c->id}" data-toggle="modal" data-target="#fn_currency_delete">
                                                    {include file='svg_icon.tpl' svgId='trash'}
                                                </button>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                            {*СОздание нового элемента*}
                            <div id="new_currency" class="fn_new_currency fn_step-3 okay_list_body sortable" style="display: none">
                                <div class="okay_list_body_item">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_num"></div>
                                        <div class="okay_list_boding okay_list_currency_name">
                                            <input name="currency[id][]" type="hidden" value=""/>
                                            <input name="currency[name][]" class="form-control" type="text" value=""/>
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_iso">
                                            <input class="form-control" name="currency[sign][]" type="text" value=""/>
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_sign">
                                            <input class="form-control" name="currency[code][]" type="text" value=""/>
                                        </div>
                                        <div class="okay_list_boding okay_list_currency_exchange">
                                            <div class="okay_list_currency_exchange_item">
                                                <div class="input-group">
                                                    <div class="input-group-qw cur_input_exchange">
                                                        <div class="input-group">
                                                            <input class="form-control"  name="currency[rate_from][]" type="text" value=""/>
                                                            <span class="input-group-addon"></span>
                                                        </div>
                                                    </div>

                                                    <div class="input-group-qw"> <span class="equality">=</span> </div>

                                                    <div class="input-group-qw cur_input_exchange">
                                                        <div class="input-group">
                                                            <input class="form-control"  name="currency[rate_to][]" type="text" value=""/>
                                                            <span class="input-group-addon">{$currency->sign}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="okay_list_boding okay_list_status"></div>
                                        <div class="okay_list_setting cur_settings"></div>
                                        <div class="okay_list_boding okay_list_close"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="action" class="okay_list_footer">
                            <div class="okay_list_foot_left"></div>
                            <input type=hidden name=recalculate value='0'>
                            <input type=hidden name=action value=''>
                            <input type=hidden name=action_id value=''>
                            <button id="apply_action" type="submit" class="btn btn_small btn_blue">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon alert--warning">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_warning|escape}</div>
                <p>{$btr->currency_message|escape}</p>
            </div>
        </div>
    </div>
</div>

<a data-toggle="modal" data-target="#fn_currency_recalculate" class="hidden"></a>
<div id="fn_currency_delete" class="modal fade show" role="document">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="card-header">
                <div class="heading_modal">{$btr->general_confirm_delete|escape}</div>
            </div>
            <div class="modal-body">
                <button type="submit" class="btn btn_small btn_blue fn_delete_currency_confirm mx-h">
                    {include file='svg_icon.tpl' svgId='checked'}
                    <span>{$btr->index_yes|escape}</span>
                </button>

                <button type="button" class="btn btn_small btn-danger fn_dismiss_currency mx-h" data-dismiss="modal">
                    {include file='svg_icon.tpl' svgId='delete'}
                    <span>{$btr->index_no|escape}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="fn_currency_recalculate" class="modal fade show" role="document">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="card-header">
                <div class="heading_modal"></div>
            </div>
            <div class="modal-body">
                <button type="submit" class="btn btn_small btn_blue fn_recalculate_currency_confirm mx-h">
                    {include file='svg_icon.tpl' svgId='checked'}
                    <span>{$btr->index_yes|escape}</span>
                </button>

                <button type="button" class="btn btn_small btn-danger fn_recalculate_currency_dismiss mx-h" data-dismiss="modal">
                    {include file='svg_icon.tpl' svgId='delete'}
                    <span>{$btr->index_no|escape}</span>
                </button>
            </div>
        </div>
    </div>
</div>

{include file='learning_hints.tpl' hintId='hint_currency'}

<script>
    var сurrency_recalculate = '{$btr->currency_recalculate|escape}';
    var сurrency_recalculate_rate = '{$btr->currency_recalculate_rate|escape}';
</script>

{* On document load *}
{literal}

<script>
    $(function() {

        var confirm = true;
        // Добавление валюты
        var curr = $('.fn_new_currency').clone(true);
        $('.fn_new_currency').remove().removeAttr('id');
        $('a#add_currency').click(function() {
            $(curr).clone(true).appendTo('#currencies_block').fadeIn('slow').find("input[name*=currency][name*=name]").focus();
            return false;
        });

        var currency_to_delete;
        $(document).on("click", ".fn_remove_currency", function () {
            currency_to_delete = $(this).data("id");
        });
        
        $(document).on("click", ".fn_delete_currency_confirm", function () {
            $('input[type="hidden"][name="action"]').val('delete');
            $('input[type="hidden"][name="action_id"]').val(currency_to_delete);
            $(".fn_form_list").submit();
        });
        
        // Подтвердили пересчет валюты
        $(document).on("click", ".fn_recalculate_currency_confirm", function () {
            $('input[name="recalculate"]').val(1);
            confirm = false;
            $(".fn_form_list").submit();
        });
        
        // Отменили пересчет валют
        $(document).on("click", ".fn_recalculate_currency_dismiss", function () {
            $('input[name="recalculate"]').val(0);
            confirm = false;
            $(".fn_form_list").submit();
        });

        // Запоминаем id первой валюты, чтобы определить изменение базовой валюты
        var base_currency_id = $('input[name*="currency[id]"]').val();

        $(".fn_form_list").submit(function() {
            if(base_currency_id != $('input[name*="currency[id]"]:first').val() && confirm) {
                $('#fn_currency_recalculate .heading_modal').text(сurrency_recalculate + ' ' + $('input[name*="name"]:first').val() + ' ' + сurrency_recalculate_rate);
                $('[data-target="#fn_currency_recalculate"]').trigger('click');
                return false;
            }
        });
    });

</script>
{/literal}
