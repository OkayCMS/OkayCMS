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
                <input class="form-control" type="text" name="settings[company]" value="{$feed->settings['company']}" />
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
        <div class="col-md-6 mb-1">
            <div class="heading_label">
                <strong>{$btr->okay_cms__feeds__feed_settings__google_merchant__price_change}</strong>
                <i class="fn_tooltips" title="{$btr->okay_cms__feeds__feed_settings__google_merchant__price_change_tooltip|escape}">
                    <svg width="20px" height="20px" viewBox="0 0 438.533 438.533" >
                        <path fill="currentColor" d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062C438.533,179.485,428.732,142.795,409.133,109.203z M255.82,356.309c0,2.662-0.862,4.853-2.573,6.563c-1.704,1.711-3.895,2.567-6.557,2.567h-54.823c-2.664,0-4.854-0.856-6.567-2.567c-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662,0.855-4.853,2.57-6.563c1.713-1.708,3.903-2.563,6.567-2.563h54.823c2.662,0,4.853,0.855,6.557,2.563c1.711,1.711,2.573,3.901,2.573,6.563V356.309z M325.338,187.574c-2.382,7.043-5.044,12.804-7.994,17.275c-2.949,4.473-7.187,9.042-12.709,13.703c-5.51,4.663-9.891,7.996-13.135,9.998c-3.23,1.995-7.898,4.713-13.982,8.135c-6.283,3.613-11.465,8.326-15.555,14.134c-4.093,5.804-6.139,10.513-6.139,14.126c0,2.67-0.862,4.859-2.574,6.571c-1.707,1.711-3.897,2.566-6.56,2.566h-54.82c-2.664,0-4.854-0.855-6.567-2.566c-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752,4.993-24.701,14.987-35.832c9.994-11.136,20.986-19.368,32.979-24.698c9.13-4.186,15.604-8.47,19.41-12.847c3.812-4.377,5.715-10.188,5.715-17.417c0-6.283-3.572-11.897-10.711-16.849c-7.139-4.947-15.27-7.421-24.409-7.421c-9.9,0-18.082,2.285-24.555,6.855c-6.283,4.565-14.465,13.322-24.554,26.263c-1.713,2.286-4.093,3.431-7.139,3.431c-2.284,0-4.093-0.57-5.424-1.709L121.35,145.89c-4.377-3.427-5.138-7.422-2.286-11.991c24.366-40.542,59.672-60.813,105.922-60.813c16.563,0,32.744,3.903,48.541,11.708c15.796,7.801,28.979,18.842,39.546,33.119c10.554,14.272,15.845,29.787,15.845,46.537C328.904,172.824,327.71,180.529,325.338,187.574z"/>
                    </svg>
                </i>
            </div>
            <div class="mb-1">
                <input class="form-control" type="number" name="settings[price_change]" value="{$feed->settings['price_change']}" />
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