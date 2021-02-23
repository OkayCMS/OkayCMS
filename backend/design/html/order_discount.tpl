<div class="okay_list fn_order_discounts_block" {if !$discounts}style="display: none;"{/if}>
    <div class="okay_list_head">
        <div class="okay_list_heading okay_list_drag"></div>
        <div class="okay_list_heading okay_list_order_discounted_name">{$btr->order_discounts_name|escape}</div>
        <div class="okay_list_heading okay_list_count">{$btr->order_discounts_procedure|escape}<i class="fn_tooltips" title="{$btr->order_discount_from_last_discount_tooltip|escape}">
            {include file='svg_icon.tpl' svgId='icon_tooltips'}
        </i></div>
        <div class="okay_list_heading okay_list_price">{$btr->order_discounts_value|escape}</div>
        <div class="okay_list_heading okay_list_order_amount_price">{$btr->order_discounts_total|escape}</div>
        <div class="okay_list_heading okay_list_close"></div>
    </div>
    <div class="okay_list_body sort_extended">
        {foreach $discounts as $discount}
            <div class="fn_row okay_list_body_item fn_sort_item">
                <div class="okay_list_row">
                    <input type="hidden" name="discount_positions[{$discount->id}]" value="{$discount->position}" />
                    <input type="hidden" name="order_discounts[id][]" value="{$discount->id}" />

                    <div class="okay_list_boding okay_list_drag move_zone">
                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                    </div>

                    <div class="okay_list_boding okay_list_order_discounted_name">
                        <div class="form_create">
                            <input  name="order_discounts[name][]" class="form-control input_create text_600" type="text" title="{$discount->name|escape}" value="{$discount->name|escape}" placeholder="{$btr->order_discount_placeholder_name|escape}">
                        </div>
                        <div class="form_create">
                            <input name="order_discounts[description][]" class="form-control input_create text_grey text_400 font_12" type="text" title="{$discount->description|escape}" value="{$discount->description|escape}" placeholder="{$btr->order_discount_placeholder_descr|escape}">
                        </div>
                    </div>
                    <div class="okay_list_boding okay_list_count hidden-md-down">
                        <div class="activity_of_switch">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch clearfix">
                                    <label class="switch switch-default">
                                        <input class="fn_discount_from_last_off" type="hidden" name="order_discounts[from_last_discount][]" value="0" {if $discount->fromLastDiscount}disabled{/if}>
                                        <input class="fn_discount_from_last_on switch-input" name="order_discounts[from_last_discount][]" value="1" type="checkbox" {if $discount->fromLastDiscount}checked{/if}>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label m-0" >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="okay_list_boding okay_list_price">
                        <div class="input-group">
                            <input type="text" class="form-control" name="order_discounts[value][]" value="{$discount->value}" />
                            <input class="fn_discount_type_input {if $discount->type == "percent"} active {/if}" type="hidden" name="order_discounts[type][]" value="percent" {if $discount->type == "absolute"}disabled{/if}/>
                            <input class="fn_discount_type_input {if $discount->type == "absolute"} active {/if}" type="hidden" name="order_discounts[type][]" value="absolute" {if $discount->type == "percent"}disabled{/if} />
                            <span class="fn_discount_change_type discount_change_type input-group-addon p-0">
                            <span class="discount_type_absolute" {if $discount->type == "percent"}style="display:none"{/if}>
                                {$currency->code|escape}
                            </span>
                            <span class="discount_type_percent" {if $discount->type == "absolute"}style="display:none"{/if}>
                                %
                            </span>
                        </span>
                        </div>
                    </div>

                    <div class="okay_list_boding okay_list_order_amount_price">
                        <div class="text_dark text_warning text_600">
                            <span class="font_16">{$discount->priceAfterDiscount|round:2}</span>
                            <span class="font_12">{$currency->sign|escape}</span>
                        </div>
                    </div>

                    <div class="okay_list_boding okay_list_close">
                        {*delete*}
                        <button data-hint="{$btr->brands_delete_brand|escape}" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile hint-anim fn_discount_remove">
                            {include file='svg_icon.tpl' svgId='trash'}
                        </button>
                    </div>
                </div>
            </div>
        {/foreach}
        <div class="fn_row okay_list_body_item fn_sort_item fn_new_order_discount">
            <div class="okay_list_row">
                <input type="hidden" name="order_discounts[id][]" value="" />

                <div class="okay_list_boding okay_list_drag ">
                </div>
                <div class="okay_list_boding okay_list_order_discounted_name">
                    <div class="form_create">
                        <input  name="order_discounts[name][]" class="form-control input_create text_600" type="text" title="" placeholder="{$btr->order_discount_placeholder_name|escape}" value="">
                    </div>
                    <div class="form_create">
                        <input name="order_discounts[description][]" class="form-control input_create text_grey text_400 font_12" type="text" title="" placeholder="{$btr->order_discount_placeholder_descr|escape}" value="">
                    </div>
                </div>
                <div class="okay_list_boding okay_list_count hidden-md-down">
                    <div class="activity_of_switch">
                        <div class="activity_of_switch_item">
                            <div class="okay_switch clearfix">
                                <label class="switch switch-default">
                                    <input class="fn_discount_from_last_off" type="hidden" name="order_discounts[from_last_discount][]" value="0">
                                    <input class="fn_discount_from_last_on switch-input" name="order_discounts[from_last_discount][]" value="1" type="checkbox">
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                                <label class="switch_label m-0" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="okay_list_boding okay_list_price">
                    <div class="input-group">
                        <input type="text" class="form-control" name="order_discounts[value][]" />
                        <input class="fn_discount_type_input active" type="hidden" name="order_discounts[type][]" value="percent" />
                        <input class="fn_discount_type_input" type="hidden" name="order_discounts[type][]" value="absolute" disabled />
                        <span class="fn_discount_change_type discount_change_type input-group-addon p-0">
                            <span class="discount_type_absolute" style="display:none">
                                {$currency->code|escape}
                            </span>
                            <span class="discount_type_percent">
                                %
                            </span>
                        </span>
                    </div>
                </div>

                <div class="okay_list_boding okay_list_order_amount_price">
                    <div class="text_dark text_warning text_600">
                        <span class="font_16"></span>
                        <span class="font_12"></span>
                    </div>
                </div>

                <div class="okay_list_boding okay_list_close">
                    {*delete*}
                    <button data-hint="{$btr->brands_delete_brand|escape}" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile hint-anim fn_discount_remove">
                        {include file='svg_icon.tpl' svgId='trash'}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="text-xs-left">
        <button type="button" class="btn btn_mini btn-info btn_openSans fn_add_order_discount">
            {include file='svg_icon.tpl' svgId='plus'}
            <span>{$btr->order_add_new_discount|escape}</span>
        </button>
    </div>
</div>
