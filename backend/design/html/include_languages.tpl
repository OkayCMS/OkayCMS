{*Список языков*}
{if $languages}
    {foreach $languages as $lang}
        <a class="flag flag_{$lang->id} {if $lang->id == $lang_id} focus{/if} hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$lang->name|escape}" href="{url lang_id=$lang->id}" data-label="{$lang->label}">
            {if is_file("{$config->lang_images_dir}{$lang->label}.png")}
                <img src="{("{$lang->label}.png")|resize:32:32:false:$config->lang_resized_dir}" width="32px;" height="32px;">
            {/if}
        </a>
    {/foreach}
{/if}
