<div class="boxed">
    <div class="permission_block">
        <div class="permission_boxes row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__google_merchant__upload_without_images|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[upload_without_images]" value='1' type="checkbox" {if $feed->settings['upload_without_images']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__google_merchant__upload_only_in_stock_products|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[upload_only_products_in_stock]" value='1' type="checkbox" {if $feed->settings['upload_only_products_in_stock']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__google_merchant__use_full_description|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[use_full_description]" value='1' type="checkbox" {if $feed->settings['use_full_description']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__google_merchant__no_export_without_price|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[no_export_without_price]" value='1' type="checkbox" {if $feed->settings['no_export_without_price']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__google_merchant__adult|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[adult]" value='1' type="checkbox" {if $feed->settings['adult']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__google_merchant__use_variant_name_like_size|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[use_variant_name_like_size]" value='1' type="checkbox" {if $feed->settings['use_variant_name_like_size']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            {get_design_block block="okay_cms__feeds__feed__settings__google_merchant__switch_checkboxes"}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                <strong>{$btr->okay_cms__feeds__feed_settings__google_merchant__company}</strong>
            </div>
            <div class="mb-1">
                <input class="form-control" type="text" name="settings[company]" value="{$feed->settings['company']|escape}" />
            </div>
        </div>
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                <strong>{$btr->okay_cms__feeds__feed_settings__google_merchant__color}</strong> <span>({$btr->okay_cms__feeds__feed_settings__google_merchant__color_notify})</span>
            </div>
            <div class="mb-1">
                <select name="settings[color]" class="selectpicker form-control">
                    <option {if $feed->settings['color'] == 0}selected=""{/if} value=""></option>
                    {foreach $features as $feature}
                        <option {if $feed->settings['color'] == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name|escape}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="heading_label">
                        <strong>{$btr->okay_cms__feeds__feed_settings__google_merchant__filter_price}</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mb-1" type="text" name="settings[filter_stock][value]" value="{$btr->okay_cms__feeds__feed_settings__google_merchant__filter_price_price}" disabled>
                        </div>
                        <div class="col-md-2">
                            <select class="selectpicker form-control mb-1" name="settings[filter_price][operator]">
                                <option value="<" {if $feed->settings['filter_price']['operator'] === '<'} selected {/if}><</option>
                                <option value=">" {if $feed->settings['filter_price']['operator'] === '>'} selected {/if}>></option>
                                <option value="=" {if $feed->settings['filter_price']['operator'] === '='} selected {/if}>=</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mb-1" type="text" name="settings[filter_price][value]" value="{$feed->settings['filter_price']['value']}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="heading_label">
                        <strong>{$btr->okay_cms__feeds__feed_settings__google_merchant__filter_stock}</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mb-1" type="text" name="settings[filter_stock][value]" value="{$btr->okay_cms__feeds__feed_settings__google_merchant__filter_stock_stock}" disabled>
                        </div>
                        <div class="col-md-2">
                            <select class="selectpicker form-control mb-1" name="settings[filter_stock][operator]">
                                <option value="<" {if $feed->settings['filter_stock']['operator'] === '<'} selected {/if}><</option>
                                <option value=">" {if $feed->settings['filter_stock']['operator'] === '>'} selected {/if}>></option>
                                <option value="=" {if $feed->settings['filter_stock']['operator'] === '='} selected {/if}>=</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mb-1" type="text" name="settings[filter_stock][value]" value="{$feed->settings['filter_stock']['value']}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {get_design_block block="okay_cms__feeds__feed__settings__google_merchant__parameters"}
    </div>
</div>

{$block = {get_design_block block="okay_cms__feeds__feed__settings__google_merchant__custom_block"}}
{if !empty($block)}
    <div class="row custom_block">
        {$block}
    </div>
{/if}