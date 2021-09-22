<div class="boxed">
    <div class="permission_block">
        <div class="permission_boxes row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span>{$btr->okay_cms__feeds__feed_settings__hotline__upload_without_images|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[upload_without_images]" value='1' type="checkbox" {if $feed->settings['upload_without_images']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span class="permission_box__label">{$btr->okay_cms__feeds__feed_settings__hotline__upload_only_in_stock_products|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[upload_only_products_in_stock]" value='1' type="checkbox" {if $feed->settings['upload_only_products_in_stock']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="permission_box permission_box--long">
                    <span class="permission_box__label">{$btr->okay_cms__feeds__feed_settings__hotline__use_full_description|escape}</span>
                    <label class="switch switch-default">
                        <input class="switch-input" name="settings[use_full_description]" value='1' type="checkbox" {if $feed->settings['use_full_description']}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
            {get_design_block block="okay_cms__feeds__feed__settings__hotline__switch_checkboxes"}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                <strong>{$btr->okay_cms__feeds__feed_settings__hotline__company}</strong>
            </div>
            <div class="mb-1">
                <input class="form-control" type="text" name="settings[company]" value="{$feed->settings['company']|escape}" />
            </div>
        </div>
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                <strong>{$btr->okay_cms__feeds__feed_settings__hotline__guarantee_manufacturer}</strong>
            </div>
            <div class="mb-1">
                <select name="settings[guarantee_manufacturer]" class="selectpicker">
                    <option {if $feed->settings['guarantee_manufacturer'] == 0}selected=""{/if} value=""></option>
                    {foreach $features as $feature}
                        <option {if $feed->settings['guarantee_manufacturer'] == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name|escape}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                {$btr->okay_cms__feeds__feed_settings__hotline__guarantee_shop}
            </div>
            <div class="mb-1">
                <select name="settings[guarantee_shop]" class="selectpicker">
                    <option {if $feed->settings['guarantee_shop'] == 0}selected=""{/if} value=""></option>
                    {foreach $features as $feature}
                        <option {if $feed->settings['guarantee_shop'] == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name|escape}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                <strong>{$btr->okay_cms__feeds__feed_settings__hotline__country_of_origin}</strong>
            </div>
            <div class="mb-1">
                <select name="settings[country_of_origin]" class="selectpicker">
                    <option {if $feed->settings['country_of_origin'] == 0}selected=""{/if} value=""></option>
                    {foreach $features as $feature}
                        <option {if $feed->settings['country_of_origin'] == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name|escape}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="heading_label">
                        <strong>{$btr->okay_cms__feeds__feed_settings__hotline__filter_price}</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mb-1" type="text" name="settings[filter_stock][value]" value="{$btr->okay_cms__feeds__feed_settings__hotline__filter_price_price}" disabled>
                        </div>
                        <div class="col-md-2">
                            <select class="selectpicker form-control mb-1" name="settings[filter_price][operator]">
                                <option value="<" {if $feed->settings['filter_price']['operator'] === '<'} selected {/if}><</option>
                                <option value=">" {if $feed->settings['filter_price']['operator'] === '>'} selected {/if}>></option>
                                <option value="=" {if $feed->settings['filter_price']['operator'] === '='} selected {/if}>=</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control" type="text" name="settings[filter_price][value]" value="{$feed->settings['filter_price']['value']}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="heading_label">
                        <strong>{$btr->okay_cms__feeds__feed_settings__hotline__filter_stock}</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mb-1" type="text" name="settings[filter_stock][value]" value="{$btr->okay_cms__feeds__feed_settings__hotline__filter_stock_stock}" disabled>
                        </div>
                        <div class="col-md-2">
                            <select class="selectpicker form-control mb-1" name="settings[filter_stock][operator]">
                                <option value="<" {if $feed->settings['filter_stock']['operator'] === '<'} selected {/if}><</option>
                                <option value=">" {if $feed->settings['filter_stock']['operator'] === '>'} selected {/if}>></option>
                                <option value="=" {if $feed->settings['filter_stock']['operator'] === '='} selected {/if}>=</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control" type="text" name="settings[filter_stock][value]" value="{$feed->settings['filter_stock']['value']}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {get_design_block block="okay_cms__feeds__feed__settings__hotline__parameters"}
    </div>
</div>

{$block = {get_design_block block="okay_cms__feeds__feed__settings__hotline__custom_block"}}
{if !empty($block)}
    <div class="row custom_block">
        {$block}
    </div>
{/if}