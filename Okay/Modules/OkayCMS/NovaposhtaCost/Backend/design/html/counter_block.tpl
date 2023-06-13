{if $settings->np_api_key_error || $uahCurrencyError}
    <div class="notif_item">
        <a href="index.php?controller=OkayCMS.NovaposhtaCost.NovaposhtaCostAdmin" class="l_notif">
            <span class="notif_icon boxed_notify">
                {include file='svg_icon.tpl' svgId='left_modules'}
            </span>
            <span class="notif_title">{$btr->left_setting_np_title|escape}</span>
        </a>
        <span class="notif_count">1</span>
    </div>
{/if}