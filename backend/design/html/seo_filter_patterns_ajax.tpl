{*Метаданные товаров категории (возвращаются аяксом)*}

<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 mt-1">
        <div class="heading_box">{$category->name|escape}</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
            <div class="alert__title mb-h">
                {$btr->alert_info|escape}
            </div>
            <div class="text_box">
                <div class="mb-1">
                    {$btr->seo_filter_patterns_ajax_message1|escape}
                    {$btr->seo_filter_patterns_ajax_message2|escape} <b style="display: inline;">{ldelim}$brand{rdelim}</b> {$btr->seo_filter_patterns_ajax_message3|escape}
                </div>
                <div class="mb-h"><b>{$btr->seo_filter_patterns_ajax_message4|escape}</b> </div>
                <div>
                    <ul class="mb-0 pl-1">
                        {literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category}</a> - {/literal}{$btr->seo_patterns_ajax_cat_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category_h1}</a> - {/literal}{$btr->seo_patterns_ajax_cat_h1|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand}</a> - {/literal}{$btr->seo_patterns_ajax_brand_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$sitename}</a> - {/literal}{$btr->seo_patterns_ajax_site_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$feature_name}</a> - {/literal}{$btr->seo_patterns_ajax_feature_name|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$feature_val}</a> - {/literal}{$btr->seo_patterns_ajax_feature_val|escape}</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$feature_name_2}</a> - {/literal}{$btr->seo_patterns_ajax_feature_name|escape} 2</li>{literal}
                            <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$feature_val_2}</a> - {/literal}{$btr->seo_patterns_ajax_feature_val|escape} 2</li>{literal}
                        {/literal}

                        {if $features_aliases}
                            {foreach $features_aliases as $fa}
                                <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{literal}{$f_alias_{/literal}{$fa->variable}{literal}}{/literal}</a> - {$btr->seo_patterns_ajax_feature_name|escape} ({$fa->name|escape})</li>
                            {/foreach}
                        {/if}
                        {if $features_aliases}
                            {foreach $features_aliases as $fa}
                                <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{literal}{$o_alias_{/literal}{$fa->variable}{literal}}{/literal}</a> - {$btr->seo_patterns_ajax_feature_val|escape} ({$fa->name|escape})</li>
                            {/foreach}
                        {/if}

                    </ul>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="boxed">
    <div class="row">
        <div class="col-md-7 col-lg-7 col-sm-12">
            <div class="mb_mobile_seofilter">
                <div id="product_categories">
                    <div class="heading_label">{$btr->seo_filter_patterns_copy_from_category|escape}:</div>
                    <select name="category_from_copy_id" class="selectpicker_for_copy form-control mb-1" data-live-search="true" data-size="10">
                        <option value='0'>{$btr->category_select|escape}</option>
                        {function name=category_select level=0}
                            {foreach $cats as $cat}
                                <option value='{$cat->id}'  {if $category->id == $cat->id}disabled{/if}>{section name=sp loop=$level}--{/section}{$cat->name}</option>
                                {category_select cats=$cat->subcategories level=$level+1}
                            {/foreach}
                        {/function}
                        {category_select cats=$categories_for_copy}
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-lg-5 col-sm-12 float-sm-right mt-1">
            <button type="button" class="btn btn_small btn_blue float-md-right fn_copy_seo_templates">
                <span>{$btr->seo_filter_patterns_copy_patterns|escape}</span>
            </button>
        </div>
    </div>
</div>

<div class="boxed">
    <div class="row">
        <div class="col-md-3 col-lg-3 col-sm-12">
            <div class="mb_mobile_seofilter">
                <select class="selectpicker fn_pattern_type form-control" data-size="2" data-live-search="false">
                    <option value="brand">+ {$btr->seo_filter_patterns_brand}</option>
                    <option value="feature">+ {$btr->seo_filter_patterns_feature}</option>
                    <option value="brand_feature">+ {$btr->seo_filter_patterns_brand} + {$btr->seo_filter_patterns_feature}</option>
                    <option value="feature_feature">+ {$btr->seo_filter_patterns_feature} + {$btr->seo_filter_patterns_feature}</option>
                </select>
            </div>
        </div>

        <div class="col-md-4 col-sm-4 col-lg-4 col-sm-12 ">
            <div class="mb_mobile_seofilter">
                <select class="selectpicker fn_features hidden form-control" disabled=""></select>
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4 col-sm-12 ">
            <div class="mb_mobile_seofilter">
                <select class="selectpicker fn_features_second hidden form-control" disabled=""></select>
            </div>
        </div>
        <div class="col-md-5 col-lg-5 col-sm-12 float-sm-right mt-1">
            <button type="button" class="btn btn_small btn_blue float-md-right fn_add_seo_template " >
                {include file='svg_icon.tpl' svgId='plus'}
                <span>{$btr->seo_filter_patterns_add_template|escape}</span>
            </button>
        </div>
    </div>
</div>

<div class="fn_templates">
    {if $patterns}
        {foreach $patterns as $p}
            <div class="fn_{$p->type}{if $p->feature_id}_{$p->feature_id}{/if}{if $p->second_feature_id}_{$p->second_feature_id}{/if} fn_template_block">
                <div class="boxed">
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading_box fn_heading_box">
                            {$btr->seo_filter_patterns_category} +
                            {if $p->type == 'brand'}
                                {$btr->seo_filter_patterns_brand}
                            {elseif $p->type == 'feature'}
                                {$btr->seo_filter_patterns_feature}
                            {elseif $p->type == 'brand_feature'}
                                {$btr->seo_filter_patterns_brand}
                                +
                                {$btr->seo_filter_patterns_feature}
                            {elseif $p->type == 'feature_feature'}
                                {$btr->seo_filter_patterns_feature}
                                {if $p->feature}
                                    ({$p->feature->name})
                                {/if}
                                +
                                {$btr->seo_filter_patterns_feature}
                                {if $p->second_feature}
                                    ({$p->second_feature->name})
                                {/if}
                            {/if}
                            {if $p->type != 'feature_feature' && $p->feature}
                                ({$p->feature->name})
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_label">H1</div>
                                <div class="mb-1">
                                    <input name="seo_filter_patterns[h1][]" class="form-control mb-h fn_ajax_area" value="{$p->h1|escape}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="heading_label">Auto Meta-description</div>
                                <div class="mb-1">
                                    <input name="seo_filter_patterns[meta_description][]" class="form-control fn_ajax_area" value="{$p->meta_description|escape}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_label">Auto Meta-title</div>
                                <div class="mb-1">
                                    <input name="seo_filter_patterns[meta_title][]" class="form-control mb-h fn_ajax_area" value="{$p->meta_title|escape}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="heading_label">Auto Meta-keywords</div>
                                <div class="mb-1">
                                    <input name="seo_filter_patterns[meta_keywords][]" class="form-control fn_ajax_area" value="{$p->meta_keywords|escape}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="heading_label">{$btr->seo_filter_patterns_ajax_description|escape}</div>
                        <div class="mb-1">
                            <textarea name="seo_filter_patterns[description][]" class="form-control fn_ajax_area">{$p->description|escape}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <button type="button" class="fn_delete_template btn btn_mini btn-danger float-md-right" >
                                {include file='svg_icon.tpl' svgId='delete'}
                                <span>{$btr->seo_filter_patterns_delete_template|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <input name="seo_filter_patterns[type][]" class="form-control" value="{$p->type}" type="hidden" />
                <input name="seo_filter_patterns[feature_id][]" class="form-control" value="{$p->feature_id}" type="hidden" />
                <input name="seo_filter_patterns[second_feature_id][]" class="form-control" value="{$p->second_feature_id}" type="hidden" />
                <input name="seo_filter_patterns[id][]" class="form-control" value="{$p->id}" type="hidden" />
                </div>
            </div>
        {/foreach}
    {/if}

</div>

<div class="row">
    <div class="col-lg-12 col-md-12 mt-1">
        <button type="submit" class="btn btn_small btn_blue float-md-right fn_update_category" data-template_type="{if $category->id}category{else}default{/if}" data-category_id="{$category->id}">
            {include file='svg_icon.tpl' svgId='checked'}
            <span>{$btr->general_apply|escape}</span>
        </button>
    </div>
</div>
