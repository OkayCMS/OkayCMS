{$block = {get_design_block block="okay_cms__feeds__feed__settings__custom_block"}}
{if !empty($block)}
    <div class="row custom_block">
        {$block}
    </div>
{/if}
<div class="fn_settings" data-preset_name="{if $feed}{$feed->preset}{else}{$settings_templates|key}{/if}">
    {$settings_template}
</div>

{literal}
    <script>
        $(function() {
            $('.selectpicker_categories').selectpicker();
            $('.selectpicker_brands').selectpicker();

            let newSettings = [];

            $('.fn_new_settings_container .fn_new_settings').each(function(i, el) {
                newSettings[$(el).data('preset_name')] = el;
            });

            $(document).on('change', 'select.fn_preset_select', function() {
                let currentEl = $('.fn_settings_container .fn_settings');
                newSettings[currentEl.data('preset_name')] = currentEl[0];
                currentEl.appendTo('.fn_new_settings_container');
                $('.fn_settings_container').append(newSettings[$(this).val()]);
            })
        })
    </script>
{/literal}