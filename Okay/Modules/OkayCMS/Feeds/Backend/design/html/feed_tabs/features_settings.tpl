{if $feed}
    <div class="row">
        <div class="col-md-12">
            <div class="boxed">
                {foreach $features as $feature}
                    <div class="row fn_feature_settings feature_settings pt-1">
                        <input type="hidden" name="entity_id" value="{$feature->id}">
                        <div class="col-md-3">
                            {$feature->name}
                        </div>
                        {get_design_block block="okay_cms__feeds__feed__features_settings__settings_custom_block" vars=['feature' => $feature]}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{else}
    <div class="alert alert--icon alert--warning">
        <div class="alert__content">
            <div class="alert__title">{$btr->alert_warning}</div>
            <p>{$btr->okay_cms__feeds__feed_tab_categories_settings_save_notify}</p>
        </div>
    </div>
{/if}

{literal}
    <script>
        $(function() {
            $(document).on('change', '.fn_feature_settings input, .fn_feature_settings select', function() {
                let rowForm = $(this).closest('.fn_feature_settings').clone(),
                    form = document.createElement('form');

                $(form).append(rowForm);

                let formData = new FormData(form);
                formData.append('preset', $('select.fn_preset_select').val());
                formData.append('feed_id', {/literal}{$smarty.get.id}{literal});
                formData.append('entity', 'feature');
                formData.append('session_id', '{/literal}{$smarty.session.id}{literal}');

                $.ajax({
                    url: '{/literal}{url controller='OkayCMS.Feeds.FeedAdmin@updateEntitySettings'}{literal}',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                }).done(function(response){
                    if (response.hasOwnProperty('success') && response.success) {
                        toastr.success('', "{/literal}{$btr->toastr_success|escape}{literal}");
                    } else {
                        toastr.error('', "{/literal}{$btr->toastr_error|escape}{literal}");
                    }
                });
            });
        })
    </script>

    <style>
        .feature_settings {
            border-top: 1px solid rgba(0, 0, 0, 0.2);
        }
    </style>
{/literal}