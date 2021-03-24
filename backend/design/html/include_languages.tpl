{*Список языков*}
{if $languages}
    {foreach $languages as $lang}
        <a class="flag flag_{$lang->id} {if $lang->id == $lang_id} focus{/if} hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$lang->name|escape}" href="{url lang_id=$lang->id}" data-label="{$lang->label|escape}">
            {if is_file("{$config->lang_images_dir|escape}{$lang->label|escape}.png")}
                <img src="{("{$lang->label|escape}.png")|resize:32:32:false:$config->lang_resized_dir}" width="32px;" height="32px;">
            {/if}
        </a>
    {/foreach}
{/if}
