<div class="row">
    <div class="col-md-12">
        <div class="boxed">
            {foreach $features as $feature}
                <div class="row feature_mapping pt-1">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input class="form-control" type="text" value="{$feature->name}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="activity_of_switch activity_of_switch">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->okay_cms__feeds__feed_tab_feature_mappings__upload}</label>
                                    <label class="switch switch-default">
                                        <input
                                            class="switch-input"
                                            {if !empty($feature_mappings[$feature->id])}
                                                name="mappings[{$feature_mappings[$feature->id]->id}][to_feed]"
                                            {else}
                                                name="new_mappings[feature][{$feature->id}][to_feed]"
                                            {/if}
                                            value="1"
                                            type="checkbox"
                                            {if empty($feature_mappings[$feature->id]) || !empty($feature_mappings[$feature->id]->to_feed)} checked {/if}
                                        >
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <input
                                    {if !empty($feature_mappings[$feature->id])}
                                        name="mappings[{$feature_mappings[$feature->id]->id}][value]"
                                    {else}
                                        name="new_mappings[feature][{$feature->id}][value]"
                                    {/if}
                                    class="form-control fn_feature_mapping"
                                    type="text"
                                    placeholder="{$feature->name}"
                                    value="{$feature_mappings[$feature->id]->value}"
                                    {if !empty($feature_mappings[$feature->id]->value)} readonly="readonly" {/if}
                                >
                                <span class="input-group-addon disable_feature_mapping fn_disable_feature_mapping"><i class="fa fa-lock {if empty($feature_mappings[$feature->id]->value)} fa-unlock {/if}"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

{literal}
    <script>
        $(document).on("click", ".fn_disable_feature_mapping", function () {
            let input = $(this).closest('.input-group').find('.fn_feature_mapping');
            if(input.attr("readonly")){
                input.removeAttr("readonly");
            } else {
                input.attr("readonly",true);
            }
            $(this).find('i').toggleClass("fa-unlock");
        });
    </script>

    <style>
        .disable_feature_mapping {
            cursor: pointer;
        }

        .feature_mapping {
            border-top: 1px solid rgba(0, 0, 0, 0.2);
        }
    </style>
{/literal}