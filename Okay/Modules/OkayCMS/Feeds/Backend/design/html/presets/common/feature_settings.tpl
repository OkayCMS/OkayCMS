<div class="settings_added_design_elements">
    <div class="form-group">
        <div class="heading_label">
            <span>{$btr->okay_cms__feeds__feed__categories_settings__common__name_in_feed}</span>
        </div>
        <input name="name_in_feed" class="form-control" type="text" placeholder="{$feature->name}" value="{$feed->features_settings[$feature->id]['name_in_feed']}" >
    </div>
</div>
<div class="settings_added_design_elements">
    <div class="form-group">
        <div class="activity_of_switch activity_of_switch--left">
            <div class="activity_of_switch_item">
                <div class="okay_switch okay_switch--nowrap clearfix">
                    <label class="switch switch-default">
                        <input
                            class="switch-input"
                            name="to_feed"
                            value="1"
                            type="checkbox"
                            {if !$feed->features_settings[$feature->id] || $feed->features_settings[$feature->id]['to_feed']} checked {/if}
                        >
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                    <label class="switch_label switch_label--right">
                        <span>{$btr->okay_cms__feeds__feed__features_settings__common__to_feed}</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
