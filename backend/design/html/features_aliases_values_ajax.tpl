{if $features_values}
    <div class="row">
        {*Блок алиасов значений свойств*}
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->feature_options_aliases|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card row options_aliases">
                    <div class="col-lg-12 col-md-12">
                        <div class="okay_list">
                            <div class="okay_list_head">
                                <div class="okay_list_heading feature_option_name">{$btr->feature_feature_alias_value|escape}</div>
                                <div class="okay_list_heading feature_option_aliases">{$btr->feature_option_aliases_value|escape}</div>
                            </div>
                            <div class="okay_list_body">
                                {foreach $features_values as $fv}
                                    <div class="fn_row okay okay_list_body_item">
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding feature_option_name">
                                                <div class="heading_box visible_xs">{$btr->feature_feature_alias_value|escape}</div>
                                                {$fv->value|escape}
                                            </div>
                                            <div class="okay_list_boding feature_option_aliases">
                                                <div class="heading_box visible_xs">{$btr->feature_option_aliases_value|escape}</div>
                                                {foreach $features_aliases as $fa}
                                                    <div class="feature_opt_aliases_list">
                                                        <div class="heading_label option_alias_name">{$fa->name|escape}</div>
                                                        <div class="option_alias_value">
                                                            <input type="text" class="form-control" name="options_aliases[{$fv->translit}][{$fa->id}]" value="{$fv->aliases[{$fa->id}]->value|escape}">
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
<div class="row">
    <div class="col-lg-12 col-md-12 mb-2">
        <button type="submit" class="btn btn_small btn_blue float-md-right fn_save_aliases">
            {include file='svg_icon.tpl' svgId='checked'}
            <span>{$btr->general_apply|escape}</span>
        </button>
    </div>
</div>
