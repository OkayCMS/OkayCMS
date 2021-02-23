{*Метаданные товаров категории (возвращаются аяксом)*}
<div class="heading_box">{$category->name|escape}</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
            <div class="alert__title mb-h">
                {$btr->alert_info|escape}
            </div>
            <div class="text_box">
                <div class="mb-1">
                    {$btr->seo_patterns_ajax_message1|escape}<br>
                    {$btr->seo_patterns_ajax_message2|escape} <b style="display: inline;">{ldelim}$brand{rdelim}</b> {$btr->seo_patterns_ajax_message3|escape}
                </div>
                <div class="mb-h"><b>{$btr->seo_patterns_ajax_message4|escape}</b> </div>
                <div>
                    <ul class="mb-0 pl-1">
                        {literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category}</a>
                                 - {/literal}{$btr->seo_patterns_ajax_cat_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category_h1}</a> - {/literal}{$btr->seo_patterns_ajax_cat_h1|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand}</a> - {/literal}{$btr->seo_patterns_ajax_brand_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$product}</a> - {/literal}{$btr->seo_patterns_ajax_product_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$price}</a> - {/literal}{$btr->seo_patterns_ajax_product_price|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$compare_price}</a> - {/literal}{$btr->seo_patterns_ajax_product_compare_price|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$sku}</a> - {/literal}{$btr->seo_patterns_ajax_product_sku|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$sitename}</a> - {/literal}{$btr->seo_patterns_ajax_site_name|escape}</li>{literal}
                        {/literal}
                        {foreach $features as $feature}
                            {if $feature->auto_name_id && $feature->auto_value_id}
                                <li>
                                    <span>"{$feature->name}": </span>
                                    <span>{$btr->general_name|escape} - {literal}{${/literal}{$feature->auto_name_id}{literal}}{/literal};</span>
                                    <span>{$btr->seo_patterns_ajax_value|escape} - {literal}{${/literal}{$feature->auto_value_id}{literal}}{/literal}</span>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<div class="fn_step-3 row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <div class="heading_label">Auto Meta-title</div>
                <div class="mb-1">
                    <input name="auto_meta_title" class="form-control mb-h fn_ajax_area" value="{$category->auto_meta_title|escape}" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="heading_label">Auto Meta-keywords</div>
                <div class="mb-1">
                    <input name="auto_meta_keywords" class="form-control fn_ajax_area" value="{$category->auto_meta_keywords|escape}" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="heading_label">Auto H1</div>
                <div class="mb-1">
                    <input name="auto_h1" class="form-control fn_ajax_area" value="{$category->auto_h1|escape}" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <div class="heading_label">Auto Meta-description</div>
                <div class="mb-1">
                     <textarea name="auto_meta_desc" class="form-control okay_textarea fn_ajax_area">{$category->auto_meta_desc|escape}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_label">{$btr->seo_patterns_ajax_products_description|escape}</div>
        <div class="mb-1">
            <textarea name="auto_description" class="form-control okay_textarea fn_ajax_area">{$category->auto_description|escape}</textarea>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 mt-1">
        <button type="submit" class="btn btn_small btn_blue float-md-right fn_update_category" data-template_type="{if $category->id}category{else}default{/if}" data-category_id="{$category->id}">
            {include file='svg_icon.tpl' svgId='checked'}
            <span>{$btr->general_apply|escape}</span>
        </button>
    </div>
</div>

