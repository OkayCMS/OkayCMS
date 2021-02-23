{if $purchase->discounts}
    <div class="order_discounted_block" style="display: none;">
        <div class="okay_list_body order_discounted_block__inner sort_extended">
            {foreach $purchase->discounts as $discount}
                <div class="fn_row okay_list_body_item fn_sort_item">
                    <div class="okay_list_row">
                        <input type="hidden" name="discount_positions[{$discount->id}]" value="{$discount->position}" />

                        <div class="okay_list_boding okay_list_drag move_zone">
                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                        </div>

                        <div class="okay_list_boding okay_list_order_discounted_name">
                            <div class="input-group">
                                <input name="discounts[{$discount->id}][name]" class="form-control" type="text" value="{$discount->name|escape}">
                                <a class="fn_tooltips input-group-addon p-0" title="{$discount->description|escape}" href="javascript:;" data-src="#popup_order_discount_text_{$purchase->product->id}" data-fancybox="hello_{$purchase->product->id}">
                                    {include file='svg_icon.tpl' svgId='order_list'}
                                </a>
                            </div>
                            <div style="display: none;">
                                <div id="popup_order_discount_text_{$purchase->product->id}" class="popup popup_animated">
                                    <div class="popup__heading">
                                        <span data-language="purchase_discount__popup_title">Описание скидки</span>
                                    </div>
                                    <div class="popup__description">
                                        <textarea class="form-control long_textarea" name="discounts[{$discount->id}][description]">
                                            {$discount->description|escape}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="okay_list_boding okay_list_count hidden-md-down">
                            <div class="activity_of_switch">
                                <div class="activity_of_switch_item">
                                    <div class="okay_switch clearfix">
                                         <label class="switch switch-default">
                                            <input class="switch-input" name="discounts[{$discount->id}][from_last_discount]" value="1" type="checkbox" {if $discount->fromLastDiscount}checked{/if}>
                                            <span class="switch-label"></span>
                                            <span class="switch-handle"></span>
                                        </label>
                                        <label class="switch_label m-0" >
                                            {*$btr->order_discount_from_last_discount*}
                                            <i class="fn_tooltips" title="{$btr->order_discount_from_last_discount_tooltip|escape}">
                                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                            </i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="okay_list_boding okay_list_price">
                            <div class="input-group">
                                <input type="text" class="form-control" name="discounts[{$discount->id}][value]" value="{$discount->value}" />
                                <span class="input-group-addon p-0">
                                    {if $discount->type == 'absolute'}
                                        {$currency->code|escape}
                                    {else}
                                        %
                                    {/if}
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
        </div>
    </div>
{/if}
