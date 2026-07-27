{$meta_title = $btr->okaycms__liqpay__description_title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okaycms__liqpay__description_title|escape}
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
                        {if $message_success == 'saved'}
                            {$btr->general_settings_saved|escape}
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

<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    <div class="row row--xxl">
        <div class="col-lg-8 col-md-12">
            <div class="alert alert--icon alert--info">
                <div class="alert__content">
                    <div class="alert__title">{$btr->alert_info|escape}</div>
                    <p>{$btr->okaycms__liqpay__description_part_1}:
                        <b>{url_generator route="OkayCMS_LiqPay_callback" absolute=1}</b> {$btr->okaycms__liqpay__description_part_2}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_np_options|escape}
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mb-1">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch okay_switch--nowrap clearfix">
                                    <label class="switch switch-default mr-1">
                                        <input class="switch-input" name="liq_pay_pay_send_receipts_client_after_fiscalization" value='1' type="checkbox"
                                               {if $settings->liq_pay_pay_send_receipts_client_after_fiscalization}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label mr-0">{$btr->liq_pay_pay_send_receipts_client_after_fiscalization}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 mb-1">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch okay_switch--nowrap clearfix">
                                    <label class="switch switch-default mr-1">
                                        <input class="switch-input" name="liq_pay_pay_send_receipts_admin_after_fiscalization" value='1' type="checkbox"
                                               {if $settings->liq_pay_pay_send_receipts_admin_after_fiscalization}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label mr-0">{$btr->liq_pay_pay_send_receipts_admin_after_fiscalization}</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn_small btn_blue ml-1">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>



<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <div>
                <img src="{$rootUrl}/Okay/Modules/OkayCMS/LiqPay/Backend/design/images/liqpay.png">
            </div>
        </div>
    </div>
</div>
