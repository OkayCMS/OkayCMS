{* Title *}
{$meta_title=$btr->discounts_settings_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->discounts_settings_title|escape}</div>
    </div>
</div>

<div class="row d_flex">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_description|escape}</div>
                <p>Описание модуля <a href="https://github.com/OkayCMS/Okay3/tree/master/docs/discounts_management.md" target="_blank">Docs</a></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <form class="sets_form" method="post">
        <div class="sets_block col-lg-6 col-md-12 pr-0">
            <input type="hidden" name="session_id" value="{$smarty.session.id}">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->discounts_settings_purchase_sets|escape}
                    <i class="fn_tooltips" title="{$btr->discounts_settings_purchase_sets_tooltip|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="box_btn_heading ml-1">
                        <button type="button" class="btn btn_mini btn-secondary btn_openSans fn_add_purchase_set">
                            {include file='svg_icon.tpl' svgId='plus'}
                            <span>{$btr->discounts_settings_add_set|escape}</span>
                        </button>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="alert alert--icon alert--info">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->discounts_settings_purchase_signs_title}</div>
                            {foreach $registered_signs['purchase'] as $discount}
                                <div>
                                    <span>
                                        <a href="test" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$discount->sign}</a>
                                    </span>:
                                    <span>{$discount->name}</span> :
                                    <span>{$discount->description}</span>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                    <div class="okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_discounted_stg_sts_name">{$btr->discounts_settings_set|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        <div class="fn_set_list fn_sort_list okay_list_body sortable">
                            {if $purchase_sets}
                                {foreach $purchase_sets as $set}
                                    <div class="fn_row okay_list_body_item">
                                        <div class="okay_list_row fn_sort_item">
                                            <div class="okay_list_boding okay_list_drag move_zone">
                                                {include file='svg_icon.tpl' svgId='drag_vertical'}
                                            </div>
                                            <div class="okay_list_boding okay_list_discounted_stg_sts_name">
                                                <input type="text" class="form-control" name="purchase_sets[sets][]" value="{$set->set|escape}">
                                            </div>
                                            <div>
                                                <label>
                                                    <span>{$btr->discounts_settings_partial_application}</span>
                                                </label>
                                                <input class="fn_partial_checkbox_not" name="purchase_sets[partial][]" type="hidden" value="0" {if $set->partial}disabled{/if}>
                                                <input class="fn_partial_checkbox" name="purchase_sets[partial][]" type="checkbox" value="1" {if $set->partial}checked{/if}>
                                            </div>
                                            <div class="okay_list_boding okay_list_close">
                                                {*delete*}
                                                <button data-hint="{$btr->discounts_settings_delete_set|escape}" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile">
                                                    {include file='svg_icon.tpl' svgId='trash'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}

                            <div class="fn_row fn_new_purchase_set fn_sort_item okay_list_body">
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row fn_sort_item">
                                        <div class="okay_list_boding okay_list_drag"></div>
                                        <div class="okay_list_boding okay_list_discounted_stg_sts_name">
                                            <input type="text" class="form-control" name="purchase_sets[sets][]">
                                        </div>
                                        <div>
                                            <label>
                                                <span>{$btr->discounts_settings_partial_application}</span>
                                            </label>
                                            <input class="fn_partial_checkbox_not" name="purchase_sets[partial][]" type="hidden" value="0">
                                            <input class="fn_partial_checkbox" name="purchase_sets[partial][]" type="checkbox" value="1">
                                        </div>
                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->discounts_settings_delete_set|escape}" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile">
                                                {include file='svg_icon.tpl' svgId='trash'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sets_block col-lg-6 col-md-12">
            <input type="hidden" name="session_id" value="{$smarty.session.id}">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->discounts_settings_cart_sets|escape}
                    <i class="fn_tooltips" title="{$btr->discounts_settings_cart_sets_tooltip|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="box_btn_heading ml-1">
                        <button type="button" class="btn btn_mini btn-secondary btn_openSans fn_add_cart_set">
                            {include file='svg_icon.tpl' svgId='plus'}
                            <span>{$btr->discounts_settings_add_set|escape}</span>
                        </button>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="alert alert--icon alert--info">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->discounts_settings_cart_signs_title}</div>
                            {foreach $registered_signs['cart'] as $discount}
                                <div>
                                    <span>
                                        <a href="test" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$discount->sign}</a>
                                    </span>:
                                    <span>{$discount->name}</span> :
                                    <span>{$discount->description}</span>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                    <div class="okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_discounted_stg_sts_name">{$btr->discounts_settings_set|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        <div class="fn_set_list fn_sort_list okay_list_body sortable">
                            {if $cart_sets}
                                {foreach $cart_sets as $set}
                                    <div class="fn_row okay_list_body_item">
                                        <div class="okay_list_row fn_sort_item">
                                            <div class="okay_list_boding okay_list_drag move_zone">
                                                {include file='svg_icon.tpl' svgId='drag_vertical'}
                                            </div>
                                            <div class="okay_list_boding okay_list_discounted_stg_sts_name">
                                                <input type="text" class="form-control" name="cart_sets[sets][]" value="{$set->set|escape}">
                                            </div>
                                            <div>
                                                <label>
                                                    <span>{$btr->discounts_settings_partial_application}</span>
                                                </label>
                                                <input class="fn_partial_checkbox_not" name="cart_sets[partial][]" type="hidden" value="0" {if $set->partial}disabled{/if}>
                                                <input class="fn_partial_checkbox" name="cart_sets[partial][]" type="checkbox" value="1" {if $set->partial}checked{/if}>
                                            </div>
                                            <div class="okay_list_boding okay_list_close">
                                                {*delete*}
                                                <button data-hint="{$btr->discounts_settings_delete_set|escape}" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile">
                                                    {include file='svg_icon.tpl' svgId='trash'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}

                            <div class="fn_row fn_new_cart_set fn_sort_item okay_list_body">
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row fn_sort_item">
                                        <div class="okay_list_boding okay_list_drag"></div>
                                        <div class="okay_list_boding okay_list_discounted_stg_sts_name">
                                            <input type="text" class="form-control" name="cart_sets[sets][]">
                                        </div>
                                        <div>
                                            <label>
                                                <span>{$btr->discounts_settings_partial_application}</span>
                                            </label>
                                            <input class="fn_partial_checkbox_not" name="cart_sets[partial][]" type="hidden" value="0">
                                            <input class="fn_partial_checkbox" name="cart_sets[partial][]" type="checkbox" value="1">
                                        </div>
                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->discounts_settings_delete_set|escape}" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile">
                                                {include file='svg_icon.tpl' svgId='trash'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <button type="submit" value="labels" class="btn btn_small btn_blue float-md-right">
                {include file='svg_icon.tpl' svgId='checked'}
                <span>{$btr->general_apply|escape}</span>
            </button>
        </div>
    </form>
</div>

{* On document load *}
<script>
    $(function(){
        $(document).on('click', '.btn_close', function(){
            $(this).closest('.fn_row').remove();
        });

        let new_purchase_set = $(".fn_new_purchase_set").clone(true);
        $(".fn_new_purchase_set").remove();

        let new_cart_set = $(".fn_new_cart_set").clone(true);
        $(".fn_new_cart_set").remove();

        $(document).on("click", ".fn_add_purchase_set", function () {
            clone_set = new_purchase_set.clone(true);
            clone_purchase_classes = clone_set.addClass("fn_ancor_label");
            $(this).closest('.sets_block').find(".fn_set_list").append(clone_set);

            setTimeout(function () {
                setChanges2();
            }, 100);

            function setChanges2() {
                $(clone_purchase_classes).each(function () {
                    $('html, body').animate({
                        scrollTop: clone_purchase_classes.offset().top - 70
                    }, 2000);
                });
            }
        });

        $(document).on("click", ".fn_add_cart_set", function () {
            clone_set = new_cart_set.clone(true);
            clone_cart_classes = clone_set.addClass("fn_ancor_label");
            $(this).closest('.sets_block').find(".fn_set_list").append(clone_set);

            setTimeout(function () {
                setChanges();
            }, 100);

            function setChanges() {
                $(clone_cart_classes).each(function () {
                    $('html, body').animate({
                        scrollTop: clone_cart_classes.offset().top - 70
                    }, 2000);
                });
            }
        });

        let registeredSigns = {json_encode($registered_signs)},
            regex = /\$<?([A-z0-9][A-z0-9]*)/g;

        $('.sets_form input').each(function(i, el){
            if (!validateSet(el.value)) {
                $(el).addClass('error');
            }
        });

        $(document).on('change', '.sets_form input', function(){
            $(this).removeClass('error');
            if (!validateSet($(this).val())) {
                $(this).addClass('error');
            }
        });

        $(document).on('change', '.fn_partial_checkbox', function(){
            if ($(this).is(':checked')) {
                $(this).closest('.fn_row').find('.fn_partial_checkbox_not').prop('disabled', true);
            } else {
                $(this).closest('.fn_row').find('.fn_partial_checkbox_not').prop('disabled', false);
            }
        });

        function validateSet(set) {
            let match;
            while (match = regex.exec(set)) {
                if (!registeredSigns.purchase.hasOwnProperty(match[1]) && !registeredSigns.cart.hasOwnProperty(match[1])) {
                    regex.lastIndex = 0;
                    return false;
                }
            }
            regex.lastIndex = 0;
            return true;
        }

        sclipboard();
    })
</script>
