<input type="hidden" name="feature_id" value="{$feature->id}" />
<div class="min_height_210px">
    <div class="heading_box">
        {$btr->feature_feature_aliases|escape}
    </div>
    <div class="fn_sort_list">
        <div class="alert alert--icon alert--error">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_error|escape}</div>
                <p>{$btr->feature_delete_alias_notice|escape}</p>
            </div>
        </div>

        <div class="okay_list ok_related_list">
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_drag"></div>
                <div class="okay_list_heading feature_alias_name">{$btr->feature_feature_alias_name}</div>
                <div class="okay_list_heading feature_alias_variable">{$btr->feature_feature_alias_variable}</div>
                <div class="okay_list_heading feature_alias_value">{$btr->feature_feature_alias_value}</div>
                <div class="okay_list_heading okay_list_close"></div>
            </div>
            <div class="okay_list_body fn_feature_aliases_list sortable">

                {foreach $features_aliases as $fa}
                    <div class="fn_row okay okay_list_body_item fn_sort_item">
                        <div class="okay_list_row">
                            <input type="hidden" class="fn_feature_alias_id" name="features_aliases[id][]" value="{$fa->id|escape}">
                            <div class="okay_list_boding okay_list_drag move_zone">
                                {include file='svg_icon.tpl' svgId='drag_vertical'}
                            </div>
                            <div class="okay_list_boding feature_alias_name">
                                <div class="heading_label visible_md">{$btr->feature_feature_alias_name}</div>
                                <input type="text" class="form-control fn_feature_alias_name" name="features_aliases[name][]" value="{$fa->name|escape}">
                            </div>
                            <div class="okay_list_boding feature_alias_variable">
                                <div class="heading_label visible_md">{$btr->feature_feature_alias_variable}</div>
                                <input type="text" class="form-control fn_feature_alias_variable" name="" value="{literal}{$f_alias_{/literal}{$fa->variable|escape}{literal}}{/literal}" readonly="">
                            </div>
                            <div class="okay_list_boding feature_alias_value">
                                <div class="heading_label visible_md">{$btr->feature_feature_alias_value}</div>
                                <input type="text" class="form-control" name="feature_aliases_value[value][]" value="{$fa->value->value|escape}">
                                <input type="hidden" name="feature_aliases_value[id][]" value="{$fa->value->id|escape}">
                            </div>
                            <div class="okay_list_boding okay_list_close">
                                <button data-hint="{$btr->feature_delete_alias|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                    {include file='svg_icon.tpl' svgId='delete'}
                                    <span class="visible_md">{$btr->feature_delete_alias|escape}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
        <div class="mt-1">
            <button type="button" class="btn btn_small btn-info fn_add_feature_alias">
                {include file='svg_icon.tpl' svgId='plus'}
                <span>{$btr->feature_add_feature_alias|escape}</span>
            </button>

            <button type="submit" class="btn btn_small btn_blue float-md-right fn_save_aliases">
                {include file='svg_icon.tpl' svgId='checked'}
                <span>{$btr->general_apply|escape}</span>
            </button>
        </div>
    </div>
</div>
